import { getConnection, Brackets } from 'typeorm';

// Entities
import { MoLogs } from '../../db/entity/moLogs';
import { MoAdmins } from '../../db/entity/moAdmins';

export default class Logs {
    private offset: int;
    private limit: int;

    constructor() {
        this.offset = 0;
        this.limit = 20;
    }

    public async getLogs(attributes: array): JSON {
        const returnLogs = {
            logs: [],
            amount: 0
        };

        // Calculating offset
        this.offset = this.limit * (attributes.page - 1);

        try {
            // Fetching logs
            const logs = await getConnection()
                .createQueryBuilder()
                .select('l')
                .from(MoLogs, 'l')
                .leftJoinAndMapOne('l.admin', MoAdmins, 'a', 'a.id = l.userId')
                .where(new Brackets((qb): WhereExpression => {
                    if (attributes.module) {
                        qb.where('l.module = :module', { module: attributes.module });
                    }
                }))
                .andWhere(new Brackets((qb): WhereExpression => {
                    if (attributes.type) {
                        qb.where('l.type = :type', { type: attributes.type });
                    }
                }))
                .offset(this.offset)
                .limit(this.limit)
                .orderBy('l.id', 'DESC')
                .getManyAndCount();

            for (const log of logs[0]) {
                returnLogs.logs.push({
                    id: log.id,
                    module: log.module,
                    type: log.type,
                    date: log.date,
                    ip: log.ip,
                    info: log.info,
                    admin: log.admin.login
                });
            }

            // Save amount
            returnLogs.amount = logs[1];
        } catch (error) {
            console.error(error);
        }

        return returnLogs;
    }
}