// 3rd party libs
import * as express from 'express';

// Get config data
import * as config from './config.json';

// Interfaces
import { RequestInterface } from '../routing-interfaces';

export default (req: RequestInterface, res: express.Response, next: express.NextFunction): void => {
    if (config.corsDomains.indexOf(req.get('origin')) > -1) {
        res.header('Access-Control-Allow-Origin', req.get('origin'));
    }

    res.header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH');
    res.header('Access-Control-Allow-Headers', `Origin, X-Requested-With, Content-Type, Accept, ${config.corsHeaders}`);

    next();
};