import { Entity, PrimaryGeneratedColumn, Column } from "typeorm";
import { type } from "os";
import { text } from "body-parser";

@Entity()
export class MoUsersAuthAttempts {

    @PrimaryGeneratedColumn()
    id: number;

    @Column({
        length: 40,
        nullable: false
    })
    ip: string;

    @Column({
        nullable: false
    })
    timestamp: Date;

    @Column({
        type: 'tinyint',
        unsigned: true,
        default: 0,
        nullable: false
    })
    attempts: number;
}