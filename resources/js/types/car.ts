export interface CarImage {
    id: number;
    car_id: number;
    image_path: string;
    is_primary: boolean;
    sort_order: number;
    created_at: string;
    updated_at: string;
}

export interface Car {
    id: number;
    name: string;
    brand: string;
    slug: string;
    short_description: string | null;
    description: string | null;
    price_per_day: string; // decimal comes as string from Laravel
    transmission: string;
    year: number;
    seats: number;
    fuel_type: string;
    color: string;
    cars_category_id?: number | null;
    category?: { id: number; name: string; slug: string };
    horsepower?: number | null;
    engine_capacity?: string | null;
    acceleration_0_100?: string | null; // decimal is returned as string
    discount_3_days?: number;
    discount_weekly?: number;
    discount_monthly?: number;
    is_available: boolean;
    is_featured: boolean;
    sort_order: number;
    is_rented: boolean;
    seo_title?: string;
    seo_keywords?: string;
    seo_description?: string;
    formatted_price?: string;
    images?: CarImage[];
    primary_image?: CarImage[];
    created_at: string;
    updated_at: string;
}
