import { getConnection } from 'typeorm';

// Entities
import { MoMultisite } from '../../db/entity/moMultisite';

export default async (req: express.Request, res: express.Response, next: express.Next): void => {
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