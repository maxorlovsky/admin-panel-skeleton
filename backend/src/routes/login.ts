// 3rd party libs
import * as express from 'express';

// Config
import * as config from '../inc/config.json';

// Logger
import { log } from '../inc/log';

// Modules
import Login from '../modules/login';

// Interfaces
import { RequestInterface } from '../routing-interfaces';

// Define router
const router: express.Router = express.Router();

router.post('/login', async (req: RequestInterface, res: express.Response): Promise<boolean> => {
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