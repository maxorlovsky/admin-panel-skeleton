import * as express from 'express';

import AdminsModules from '../modules/admins';
import Logout from '../modules/logout';
import { log } from '../inc/log';

// Define router
const router: express.Router = express.Router();

router.post('/logout', async (req: express.Request, res: express.Response): JSON => {
    if (!req.isLogged || !req.user.id) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    // Fetching proper user to pass to cleanAuth method
    const adminsModules = new AdminsModules(req.user);
    const admin = await adminsModules.getAdmin(req.user.id);

    // Login user out
    Logout.cleanAuth(admin);

    await log({
        userId: req.user.id,
        module: 'logout',
        type: 'success',
        info: `Success logout <b>${req.user.login}</b>`
    });

    // Passing session token to the user
    res.status(200).json({ data: [] });
});

export default router;