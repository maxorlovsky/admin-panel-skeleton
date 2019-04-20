import { Entity, PrimaryGeneratedColumn, Column } from 'typeorm';

@Entity()
export class MoLogs {

    @PrimaryGeneratedColumn()
    id: number;

    @Column({
        length: 100,
        nullable: true
    })
    module: string;

    @Column({
        length: 100,
        nullable: true
    })
    type: string;

    @Column({
        nullable: true
    })
    userId: number;

    @Column({
        nullable: true
    })
    date: Date;

    @Column({
        nullable: true,
        length: 40
    })
    ip: string;

    @Column({
        nullable: true,
        length: 500
    })
    info: string;
}