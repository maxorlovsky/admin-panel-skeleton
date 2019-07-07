// Entities
import { MoLogs } from '../../db/entity/moLogs';

export interface LogsAttributesInterface {
    module: string;
    type: string;
    page: number;
}

export interface GetLogsInterface {
    logs: Array<LogInterface>;
    amount: number;
}

interface LogInterface extends MoLogs {
    admin: string;
}