// Get db connection
import 'reflect-metadata';
import { createConnection } from 'typeorm';
import * as ormConfig from '../../ormconfig.json';

createConnection({
    type: 'mariadb',
    host: ormConfig.host,
    port: ormConfig.port,
    username: ormConfig.username,
    password: ormConfig.password,
    database: ormConfig.database,
    entities: ormConfig.entities,
    synchronize: ormConfig.synchronize,
    logging: ormConfig.logging
}).then().catch((error): void => {
    console.error('Database connection error');
    console.error(error);
    process.exit(1);
});