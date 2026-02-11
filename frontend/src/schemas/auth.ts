import * as z from 'zod';
import i18n from '@/lib/i18n';

export const loginSchema = z.object({
  username: z.string().min(5, i18n.t('auth.validation.username')),
  password: z.string().min(5, i18n.t('auth.validation.password')),
  rememberMe: z.boolean(),
});

export type LoginSchema = z.infer<typeof loginSchema>;
