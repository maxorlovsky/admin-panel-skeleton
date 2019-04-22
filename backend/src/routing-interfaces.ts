// 3rd party libs
import { Request } from 'express';

// Interfaces
import { AdminInterface } from './interfaces/admins';

export interface RequestInterface extends Request {
    isLogged: boolean;
    siteId: number;
    user: AdminInterface;
    file: Array<string>;
    files: Array<Array<string>>;
}