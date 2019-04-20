import { getConnection, Not } from 'typeorm';
import { hash } from 'bcrypt';

// Config
import * as config from '../inc/config.json';

// Classes
import SharedComponents from '../shared-components';

// Entities
import { MoAdmins } from '../../db/entity/moAdmins';

export default class Admins extends SharedComponents {
    private user: array;

    constructor(user) {
        super();

        this.user = user;
    }

    public async getAdmins(): array {
        const returnAdmins = [];

        try {
            // Fetching previous attempts to login
            const admins = await getConnection().getRepository(MoAdmins)
                .find({
                    deleted: false
                });

            for (const admin of admins) {
                returnAdmins.push({
                    id: admin.id,
                    login: admin.login,
                    level: admin.level,
                    lastLogin: admin.lastLogin ? admin.lastLogin : null,
                    lastIp: admin.lastIp
                });
            }
        } catch (error) {
            console.error(error);
        }

        return returnAdmins;
    }

    public async getAdmin(id: int): JSON {
        let returnAdmin = {};

        try {
            // Fetching previous attempts to login
            const admin = await getConnection().getRepository(MoAdmins)
                .findOne({
                    deleted: false,
                    id: id
                });

            if (admin) {
                returnAdmin = {
                    id: admin.id,
                    login: admin.login,
                    level: admin.level,
                    customAccess: JSON.parse(admin.customAccess)
                };
            }
        } catch (error) {
            console.error(error);
        }

        return returnAdmin;
    }

    public async addAdmin(attributes: array): boolean {
        const formData = await this.checkForm(attributes, 'add');

        if (!formData) {
            return false;
        }

        const convertedPassword = await Admins.passwordConvert(attributes.password);
        const newAdmin = new MoAdmins();

        newAdmin.login = attributes.login;
        newAdmin.password = convertedPassword;
        newAdmin.level = attributes.level;
        newAdmin.customAccess = JSON.stringify(attributes.permissions);

        try {
            await getConnection().getRepository(MoAdmins).save(newAdmin);
        } catch (error) {
            console.error(error);

            return false;
        }

        return true;
    }

    public async editAdmin(attributes: array): boolean {
        const formData = await this.checkForm(attributes, 'edit');

        if (!formData) {
            return false;
        }

        try {
            const admin = await getConnection().getRepository(MoAdmins)
                .findOne({
                    id: attributes.id
                });

            if (attributes.password) {
                const convertedPassword = await Admins.passwordConvert(attributes.password);

                admin.password = convertedPassword;
            }

            admin.login = attributes.login;
            admin.level = attributes.level;
            admin.customAccess = JSON.stringify(attributes.permissions);

            await getConnection().getRepository(MoAdmins).save(admin);
        } catch (error) {
            console.error(error);

            return false;
        }

        return true;
    }

    public async deleteAdmin(id: int): boolean {
        const admin = await this.getAdmin(id);

        if (admin.id === this.user.id) {
            this.message += 'You can not delete yourself';

            return false;
        } else if (admin.level >= this.user.level) {
            this.message += 'Denied. Access level is low, you can not delete this admin';

            return false;
        }

        try {
            const label = await getConnection().getRepository(MoAdmins)
                .findOne({
                    id: id
                });

            label.deleted = true;

            await getConnection().getRepository(MoAdmins).save(label);
        } catch (error) {
            console.error(error);

            return false;
        }

        return true;
    }

    private async checkForm(attributes: array, type: string): boolean {
        if (type === 'add') {
            if (!attributes.login) {
                this.message += 'Login is empty<br />';
                this.fields.push('login');
            } else if (attributes.login.length > 32) {
                this.message += 'Login is too long, must be less than 32 characters<br />';
                this.fields.push('login');
            } else if (attributes.login.match(/\s/g)) {
                this.message += 'Login must not have spaces, please use underscore<br />';
                this.fields.push('login');
            }
        }

        if (type === 'add' || (type === 'edit' && attributes.password)) {
            if (!attributes.password) {
                this.message += 'Password is empty<br />';
                this.fields.push('password');
            }

            if (attributes.password.length < config.minPasswordLength) {
                this.message += `Password must be at least ${config.minPasswordLength} characters long<br />`;
                this.fields.push('password');
            }
        }

        // Check if admin that creates/updated admin is on lower level
        if (attributes.level > this.user.level) {
            this.message += 'Access level is too high, you can not manipulate admin of higher level then yourself';
            this.fields.push('level');
        }

        if ((type === 'add' && await this.checkIfAdminLoginExist(attributes)) || (type === 'edit' && await this.checkIfAdminLoginExist(attributes, attributes.id))) {
            this.message += 'Sorry, login is already taken<br />';
            this.fields.push('login');
        }

        if (this.message) {
            return false;
        }

        return true;
    }

    private async checkIfAdminLoginExist(attributes: array, id: int = 0): boolean {
        const admin = await getConnection().getRepository(MoAdmins)
            .findOne({
                login: attributes.login,
                deleted: false,
                id: Not(id)
            });

        if (admin) {
            return true;
        }

        return false;
    }

    public static async passwordConvert(password: string): string {
        let returnPassword = '';

        try {
            returnPassword = await hash(password, 10);
        } catch (error) {
            console.error(error);
        };

        return returnPassword;
    }
}