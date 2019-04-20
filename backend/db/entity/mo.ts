import { Entity, PrimaryGeneratedColumn, Column } from 'typeorm';

@Entity()
export class Mo {

    @PrimaryGeneratedColumn()
    id: number;

    @Column({
        nullable: false
    })
    setting: string;

    @Column({
        type: 'text',
        nullable: false
    })
    value: string;
}