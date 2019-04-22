export interface AdminInterface {
    id?: number;
    login: string;
    level: number;
    customAccess?: Array<string>;
}

export interface AdminFormInterface {
    id?: number;
    login: string;
    password: string;
    level: number;
    permissions: Array<string>;
}