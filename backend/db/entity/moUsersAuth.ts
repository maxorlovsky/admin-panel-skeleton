import { Entity, PrimaryGeneratedColumn, Column, OneToOne, JoinColumn } from 'typeorm';
import { MoAdmins } from './moAdmins';

@Entity()
export class MoUsersAuth {

    @PrimaryGeneratedColumn()
    id: number;

    @OneToOne((): MoAdmins => MoAdmins)
    @JoinColumn()
    user: MoAdmins;

    @Column({
        length: 100,
        nullable: false
    })
    token: string;

    @Column({
        nullable: false
    })
    timestamp: Date;
}