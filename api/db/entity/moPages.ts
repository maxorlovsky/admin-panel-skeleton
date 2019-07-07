import { Entity, PrimaryGeneratedColumn, Column } from 'typeorm';

@Entity()
export class MoPages {

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
    title: string;

    @Column({
        length: 70,
        nullable: true
    })
    metaTitle: string;

    @Column({
        length: 230,
        nullable: true
    })
    metaDescription: string;

    @Column({
        length: 300,
        nullable: false
    })
    link: string;

    @Column({
        type: 'text',
        nullable: true
    })
    text: string;

    @Column({
        nullable: false,
        unsigned: true,
        default: false
    })
    enabled: boolean;

    @Column({
        nullable: false,
        unsigned: true,
        default: false
    })
    deleted: boolean;
}