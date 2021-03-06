// 3rd party libs
import * as express from 'express';

// Logger
import { log } from '../inc/log';

// Modules
import UserData from '../modules/user-data';

// Interfacess
import { RequestInterface } from '../routing-interfaces';

// Define router
const router: express.Router = express.Router();

router.get('/me', (req: RequestInterface, res: express.Response): boolean => {
    const userData = new UserData(req.user);

    const data = userData.fetchAdminData();

    if (!data) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    // Passing session token to the user
    res.status(200).json(data);
});

router.get('/menu', async (req: RequestInterface, res: express.Response): Promise<boolean> => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    const userData = new UserData(req.user);

    const data = await userData.fetchMenu();

    if (!data) {
        res.status(401).json({ message: 'Error fetching the menu' });

        return false;
    }

    // Passing session token to the user
    res.status(200).json(data);
});

router.get('/multisite', async (req: RequestInterface, res: express.Response): Promise<boolean> => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    const userData = new UserData(req.user);

    const data = await userData.fetchMultisites();

    // Passing session token to the user
    res.status(200).json(data);
});

router.put('/user-data/change-password', async (req: RequestInterface, res: express.Response): Promise<boolean> => {
    if (!req.isLogged) {
        res.status(401).json({ message: 'Authorization error' });

        return false;
    }

    const attributes = {
        currentPass: req.body.currentPass.toString(),
        newPass: req.body.newPass.toString(),
        repeatPass: req.body.repeatPass.toString()
    };

    const userData = new UserData(req.user);

    const checkForm = await userData.updatePassword(attributes);

    if (!checkForm) {
        res.status(400).json({
            message: userData.getMessage(),
            fields: userData.getFields()
        });

        await log({
            userId: req.user.id,
            module: 'user',
            type: 'password-change',
            info: `Password change failed [<b>${userData.getMessage()}</b>]`
        });

        return false;
    }

    await log({
        userId: req.user.id,
        module: 'user',
        type: 'password-change',
        info: 'Password change success'
    });

    // Passing session token to the user
    res.status(200).json({
        state: 'success',
        message: 'Password updated'
    });
});

export default router;