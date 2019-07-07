export interface PasswordFormInterface {
    currentPass: string;
    newPass: string;
    repeatPass: string;
}

export interface MultisiteInterface {
    id: number;
    name: string;
}

export interface MenuInterface {
    url: string;
    title: string;
    icon: string;
    sublinks: Array<MenuInterface>;
}