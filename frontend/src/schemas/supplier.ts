import { z } from 'zod';

export const SupplierSchema = z
  .object({
    id: z.number().optional(),
    name: z.string(),
    manager: z.string(),
    ruc: z.string(),
    address: z.string().nullish(),
    phone: z.string().nullish(),
    email: z.string().nullish(),
    description: z.string().nullish(),
    created_at: z.string(),
    updated_at: z.string(),
    deleted_at: z.string().nullish(),
  })
  .transform((dto) => ({
    ...dto,
    id: dto.id,
    name: dto.name,
    manager: dto.manager,
    ruc: dto.ruc,
    address: dto.address ?? null,
    phone: dto.phone ?? null,
    email: dto.email ?? null,
    description: dto.description ?? null,
    createdAt: dto.created_at ?? null,
    updatedAt: dto.updated_at ?? null,
    deletedAt: dto.deleted_at ?? null,
  }));

export type Supplier = z.output<typeof SupplierSchema>;
