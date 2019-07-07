import { Entity, PrimaryGeneratedColumn, Column } from 'typeorm';

@Entity()
export class MoLabels {

    @PrimaryGeneratedColumn()
    id: number;

    @Column({
        type: 'smallint',
        nullable: false,
        unsigned: true,
        default: 0
    })
    siteId: number;

    @Column({
        length: 100,
        nullable: false
    })
    name: string;

    @Column('text')
    output: string;

    @Column({
        nullable: false,
        unsigned: true,
        default: false
    })
    deleted: boolean;
}