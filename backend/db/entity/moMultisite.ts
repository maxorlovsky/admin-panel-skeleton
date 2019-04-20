import { Entity, PrimaryGeneratedColumn, Column } from 'typeorm';

@Entity()
export class MoMultisite {

    @PrimaryGeneratedColumn()
    id: number;

    @Column({
        length: 100,
        nullable: false
    })
    name: string;
}