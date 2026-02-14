import { z } from 'zod';
import i18n from '@/lib/i18n';
import { ClinicSchema } from './clinic';

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

export const ProfileSchema = z
  .object({
    id: z.number(),
    names: z.string(),
    paternal_surname: z.string().nullish(),
    maternal_surname: z.string().nullish(),
    full_name: z.string().nullish(),
    dni: z.string(),
    phone: z.string().nullish(),
    address: z.string().nullish(),
    position: z.string(),
    hired_at: z.string(),
  })
  .transform((dto) => ({
    id: dto.id,
    names: dto.names,
    paternalSurname: dto.paternal_surname ?? null,
    maternalSurname: dto.maternal_surname ?? null,
    fullName: dto.full_name ?? null,
    dni: dto.dni,
    phone: dto.phone ?? null,
    address: dto.address ?? null,
    position: dto.position,
    hiredAt: dto.hired_at,
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
    profile: ProfileSchema.optional(),
    clinic: ClinicSchema.optional(),
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
    profile: dto.profile,
    clinic: dto.clinic,
    createdAt: dto.created_at ?? null,
    updatedAt: dto.updated_at ?? null,
    deletedAt: dto.deleted_at ?? null,
  }));

export const LoginRequest = z.object({
  username: z
    .string()
    .min(5, i18n.t('auth.validation.username.required'))
    .max(20, 'auth.validation.username.max'),
  password: z
    .string()
    .min(5, i18n.t('auth.validation.password.required'))
    .max(20, 'auth.validation.password.max'),
  rememberMe: z.boolean(),
});

export const LoginResponse = z.object({
  token: z.string(),
  type: z.string(),
  user: UserSchema,
});

export type LoginRequest = z.infer<typeof LoginRequest>;
export type LoginResponse = z.infer<typeof LoginResponse>;
export type User = z.infer<typeof UserSchema>;
export type Role = z.infer<typeof RoleSchema>;
