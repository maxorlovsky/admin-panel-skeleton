import { Entity, PrimaryGeneratedColumn, Column } from 'typeorm';

@Entity()
export class MoAdmins {

    @PrimaryGeneratedColumn()
    id: number;

    @Column({
        length: 100,
        nullable: false
    })
    login: string;

    @Column({
        length: 100,
        nullable: false
    })
    password: string;

    @Column({
        type: 'tinyint',
        nullable: false,
        unsigned: true,
        default: 1
    })
    level: number;

    @Column('text')
    customAccess: string;

    @Column({
        nullable: true
    })
    lastLogin: Date;

    @Column({
        nullable: true,
        length: 40
    })
    lastIp: string;

    @Column({
        nullable: false,
        unsigned: true,
        default: false
    })
    deleted: boolean;
}