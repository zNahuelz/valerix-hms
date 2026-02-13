import { z } from 'zod';
import i18n from '@/lib/i18n';

export const loginSchema = z.object({
  username: z.string().min(5, i18n.t('auth.validation.username')),
  password: z.string().min(5, i18n.t('auth.validation.password')),
  rememberMe: z.boolean(),
});

export const RoleSchema = z
  .object({
    id: z.number().optional(),
    name: z.string(),
    created_at: z.string().optional(),
    updated_at: z.string().optional(),
    deleted_at: z.string().nullable().optional(),
  })
  .transform((dto) => ({
    id: dto.id,
    name: dto.name,
    createdAt: dto.created_at ?? null,
    updatedAt: dto.updated_at ?? null,
    deletedAt: dto.deleted_at ?? null,
  }));

export const UserSchema = z
  .object({
    id: z.number().optional(),
    username: z.string(),
    email: z.string().nullable(),
    avatar: z.string().nullable(),
    role: RoleSchema.optional(),
    permissions: z.array(z.string()).optional(),
    profile_type: z.string(),
    created_at: z.string().optional(),
    updated_at: z.string().optional(),
    deleted_at: z.string().nullable().optional(),
  })
  .transform((dto) => ({
    id: dto.id,
    username: dto.username,
    email: dto.email ?? null,
    avatar: dto.avatar ?? null,
    role: dto.role,
    permissions: dto.permissions ?? [],
    profileType: dto.profile_type,
    createdAt: dto.created_at ?? null,
    updatedAt: dto.updated_at ?? null,
    deletedAt: dto.deleted_at ?? null,
  }));

export type LoginSchema = z.infer<typeof loginSchema>;
export type User = z.infer<typeof UserSchema>;
export type Role = z.infer<typeof RoleSchema>;
