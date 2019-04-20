import { getConnection, LessThan } from 'typeorm';
import { format, subMinutes } from 'date-fns';
import { compare } from 'bcrypt';
import { SHA256 } from 'crypto-js';
import { address } from 'ip';

// Classes
import SharedComponents from '../shared-components';
import Logout from './logout';

// Entities
import { MoAdmins } from '../../db/entity/moAdmins';
import { MoUsersAuth } from '../../db/entity/moUsersAuth';
import { MoUsersAuthAttempts } from '../../db/entity/moUsersAuthAttempts';

export default class Login extends SharedComponents {
    private availableLoginAttempts: int;

    constructor(availableLoginAttempts) {
        super();

        this.availableLoginAttempts = availableLoginAttempts;
    }

    public async login(attributes: LoginInt): string | boolean {
        if (!attributes.login) {
            this.message = 'Login is empty';

            return false;
        }

        if (!attributes.password) {
            this.message = 'Password is empty';

            return false;
        }

        if (!await this.checkBruteForce(attributes)) {
            this.message = 'Brute force detected, your IP is blocked for 5 minutes';

            return false;
        }

        const user = await this.authentication(attributes);

        if (!user) {
            this.message = 'Login or password is incorrect';

            return false;
        }

        const sessionToken = this.createToken(user);

        // Clean up user auth attempts
        this.timeOutAuthAttempts();

        return sessionToken;
    }

    private async createToken(user: MoAdmins): string {
        const token = SHA256(`${user.id} ${user.login} ${Math.random(0, 99999)} ${new Date().toISOString}`).toString();

        // Remove old auth key, just in case it's in DB
        await Logout.cleanAuth(user);

        const usersAuth = new MoUsersAuth();

        usersAuth.timestamp = new Date().toISOString();
        usersAuth.token = token;
        usersAuth.user = user;

        await getConnection().getRepository(MoUsersAuth).save(usersAuth);

        return token;
    }

    private async authentication(attributes: LoginInt): MoAdmins | null {
        // Check if such login exist
        const user = await getConnection().getRepository(MoAdmins)
            .findOne({
                login: attributes.login,
                deleted: 0
            });

        if (user && await Login.passwordVerify(attributes.password, user.password)) {
            return user;
        }

        return null;
    }

    private async checkBruteForce(): boolean {
        // Remove timed out IPs
        this.timeOutAuthAttempts(true);

        try {
            // Fetching previous attempts to login
            const response = await getConnection().getRepository(MoUsersAuthAttempts)
                .findOne({
                    ip: address()
                });

            if (response && response.attempts >= this.availableLoginAttempts) {
                // Immediately trigger response as false to block off bruteforce
                return false;
            }
            if (response) {
                // In case there were already attempts, we're incrementing the attempt
                response.attempts++;
                await getConnection().getRepository(MoUsersAuthAttempts).save(response);
            } else {
                // Saving first attempt from this IP address
                const usersAuthAttempts = new MoUsersAuthAttempts();

                usersAuthAttempts.attempts = 1;
                usersAuthAttempts.timestamp = new Date().toISOString();
                usersAuthAttempts.ip = address();
                await getConnection().getRepository(MoUsersAuthAttempts).save(usersAuthAttempts);
            }

            return true;
        } catch (error) {
            return false;
        }

        return true;
    }

    private async timeOutAuthAttempts(timer: boolean = false): boolean {
        const findByRules = {
            ip: address()
        };

        // Just in case if we need to check by timestamp
        if (timer) {
            findByRules.timestamp = LessThan(format(subMinutes(new Date(), 5), 'YYYY-MM-DD HH:mm:ss'));
        }

        // Fetching attempts
        const response = await getConnection().getRepository(MoUsersAuthAttempts).find(findByRules);

        // Cleaning up outdated attempts
        if (response) {
            await getConnection().getRepository(MoUsersAuthAttempts).remove(response);
        }

        return true;
    }

    static async passwordVerify(userSpecifiedPassword: string, dbPassword: string): boolean {
        const match = await compare(userSpecifiedPassword, dbPassword);

        if (match) {
            return true;
        }

        return false;
    }
}

interface LoginInt {
    login: string;
    password: string;
}