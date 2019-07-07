// 3rd party libs
import * as express from 'express';
import { getConnection } from 'typeorm';

// Interfaces
import { RequestInterface } from '../routing-interfaces';

// Entities
import { Mo } from '../../db/entity/mo';
import { MoUsersAuth } from '../../db/entity/moUsersAuth';

export default async (req: RequestInterface, res: express.Response, next: express.NextFunction): Promise<boolean | void> => {
    // Marking user as logged out by default
    req.isLogged = false;

    if (req.get('sessionToken')) {
        const sessionToken = req.get('sessionToken');

        // Checking if user auth token is still viable and if yes, fetching user
        const user = await getConnection().getRepository(MoUsersAuth)
            .findOne({
                where: {
                    token: sessionToken
                },
                relations: ['user']
            });

        if (user && user.user) {
            // Check if user is not deleted
            if (user.user.deleted) {
                res.status(403).json({ message: 'Access denied, admin is removed' });

                return false;
            }

            // Saving user for future reference
            req.isLogged = true;
            req.user = {
                id: user.user.id,
                login: user.user.login,
                level: user.user.level,
                customAccess: JSON.parse(user.user.customAccess)
            };

            // Breaking down current path
            const breakdown = req.originalUrl.split('/');
            const path = breakdown[1];

            const menu = await getConnection().getRepository(Mo)
                .findOne({
                    setting: 'menu'
                });

            // Getting menu permissions
            const permissions = JSON.parse(menu.value);

            // Checking if user have a level and not custom access
            if (req.user.level > 0) {
                /*
                 Searching for index of permission and if it exist, trying to get it level, by default level should be 0
                 meaning that all every endpoint is open by default
                */
                const index = permissions.findIndex((permission): boolean => permission.key === path || permission.key === `${path}s`);
                let level = 0;

                if (index >= 0) {
                    level = permissions[index].level;
                }

                // If user level is lower, then sending access denied
                if (req.user.level < level) {
                    res.status(403).json({ message: 'Access denied' });

                    return false;
                }
            } else {
                // Assuming that user have custom access
                // Special cases, to fetch stats with custom access
                req.user.customAccess.push('multisite');
                req.user.customAccess.push('menu');
                req.user.customAccess.push('me');
                req.user.customAccess.push('user-data');

                // Just for the sake of being secure, add dashboard so there wouldn't be unexpected errors
                req.user.customAccess.push('dashboard');

                // Check if user level is enough to use this API endpoint
                if (req.user.customAccess.indexOf(path) === -1 && req.user.customAccess.indexOf(`${path}s`) === -1) {
                    res.status(403).json({ message: 'Access denied' });

                    return false;
                }
            }
        }
    }

    next();
};