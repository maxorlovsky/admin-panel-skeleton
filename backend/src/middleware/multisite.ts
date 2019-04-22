// 3rd party libs
import * as express from 'express';
import { getConnection } from 'typeorm';

// Interfaces
import { RequestInterface } from '../routing-interfaces';

// Entities
import { MoMultisite } from '../../db/entity/moMultisite';

export default async (req: RequestInterface, res: express.Response, next: express.NextFunction): Promise<void> => {
    if (req.get('siteId')) {
        // Checking if user auth token is still viable and if yes, fetching user
        const multisite = await getConnection().getRepository(MoMultisite)
            .findOne({
                id: parseInt(req.get('siteId'))
            });

        if (multisite) {
            req.siteId = multisite.id;
        } else {
            req.siteId = 0;
        }
    } else {
        req.siteId = 0;
    }

    next();
};