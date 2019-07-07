// 3rd party libs
import { getConnection, Brackets, WhereExpression } from 'typeorm';

// Interfaces
import { LogsAttributesInterface, GetLogsInterface } from '../interfaces/logs';

// Entities
import { MoLogs } from '../../db/entity/moLogs';
import { MoAdmins } from '../../db/entity/moAdmins';

export default class Logs {
    private offset: number;
    private limit: number;

    constructor() {
        this.offset = 0;
        this.limit = 20;
    }

    public async getLogs(attributes: LogsAttributesInterface): Promise<GetLogsInterface> {
        const returnLogs = {
            logs: [],
            amount: 0
        };

        // Calculating offset
        this.offset = this.limit * (attributes.page - 1);

        try {
            // Fetching logs
            // eslint-disable-next-line
            const [ logs, amount ]: any = await getConnection()
                .createQueryBuilder()
                .select('l')
                .from(MoLogs, 'l')
                .leftJoinAndMapOne('l.admin', MoAdmins, 'a', 'a.id = l.userId')
                .where(new Brackets((qb): WhereExpression => {
                    if (attributes.module && attributes.type) {
                        qb.where('l.module = :module', { module: attributes.module });
                        qb.andWhere('l.type = :type', { type: attributes.type });
                    } else if (attributes.module) {
                        qb.where('l.module = :module', { module: attributes.module });
                    } else if (attributes.type) {
                        qb.where('l.type = :type', { type: attributes.type });
                    }

                    return qb;
                }))
                .offset(this.offset)
                .limit(this.limit)
                .orderBy('l.id', 'DESC')
                .getManyAndCount();

            for (const log of logs) {
                returnLogs.logs.push({
                    id: log.id,
                    module: log.module,
                    type: log.type,
                    date: log.date,
                    ip: log.ip,
                    info: log.info,
                    admin: log.admin ? log.admin.login : '--'
                });
            }

            // Save amount
            returnLogs.amount = amount;
        } catch (error) {
            console.error(error);
        }

        return returnLogs;
    }
}