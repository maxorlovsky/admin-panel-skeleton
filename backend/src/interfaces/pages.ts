export interface PagesFormInterface {
    id?: number;
    siteId?: number;
    title: string;
    metaTitle: string;
    metaDescription: string;
    link: string;
    text: string;
    enabled: boolean;
}

export interface GetPageInterface {
    id?: number;
    siteId?: number;
}