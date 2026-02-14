import { z } from 'zod';

export const ClinicSchema = z
  .object({
    id: z.number(),
    name: z.string(),
    ruc: z.string(),
    address: z.string(),
    phone: z.string(),
    created_at: z.string().optional(),
    updated_at: z.string().optional(),
    deleted_at: z.string().nullable().optional(),
  })
  .transform((dto) => ({
    id: dto.id,
    name: dto.name,
    ruc: dto.ruc,
    address: dto.address,
    phone: dto.phone,
    createdAt: dto.created_at ?? null,
    updatedAt: dto.updated_at ?? null,
    deletedAt: dto.deleted_at ?? null,
  }));

export type Clinic = z.infer<typeof ClinicSchema>;
