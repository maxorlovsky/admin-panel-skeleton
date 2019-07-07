import { getConnection } from 'typeorm';
import { address } from 'ip';

// Entities
import { MoLogs } from '../../db/entity/moLogs';

export async function log(log: LogInterface): Promise<boolean> {
    if (!log.userId) {
        log.userId = null;
    }

    if (!log.module) {
        log.module = '';
    }
    log.module = log.module.toLowerCase();

    if (!log.type) {
        log.type = '';
    }

    const newLog = new MoLogs();

    newLog.module = log.module;
    newLog.type = log.type;
    newLog.date = new Date();
    newLog.ip = address();
    newLog.info = log.info;
    newLog.userId = log.userId;

    await getConnection().getRepository(MoLogs).save(newLog);

    return true;
}

interface LogInterface {
    userId: number | null;
    module: string | null;
    type: string | null;
    info: string;
}