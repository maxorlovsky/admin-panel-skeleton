import * as express from 'express';
import * as config from '../inc/config.json';

import Login from '../modules/login';
import { log } from '../inc/log';

// Define router
const router: express.Router = express.Router();

router.post('/login', async (req: express.Request, res: express.Response): JSON => {
    // In case user is already authorized, we ignore login endpoint
    if (req.isLogged) {
        res.status(405).json({ message: 'Already authorized' });

        return false;
    }

    const attributes = {
        login: req.body.login.toString(),
        password: req.body.password.toString()
    };

    // Define controller, fill up main variables
    const login = new Login(config.availableLoginAttempts);

    // Authenticating user and in case of success return token
    const checkUser = await login.login(attributes);

    if (!checkUser) {
        res.status(400).json({ message: login.getMessage() });

        await log({
            userId: null,
            module: 'login',
            type: 'fail',
            info: `Error login as <b>${attributes.login}</b> (${login.getMessage()})`
        });

        return false;
    }

    await log({
        userId: null,
        module: 'login',
        type: 'success',
        info: `Success login as <b>${attributes.login}</b>`
    });

    // Passing session token to the user
    res.status(200).json({ sessionToken: checkUser });
});

export default router;