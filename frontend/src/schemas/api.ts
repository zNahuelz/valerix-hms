import { z } from 'zod';

export const ErrorResponse = z
  .object({
    message: z.string().nullish().optional(),
    code: z.string().nullish().optional(),
    locked_until: z.string().nullish().optional(),
  })
  .transform((dto) => ({
    message: dto.message,
    code: dto.code,
    lockedUntil: dto.locked_until,
  }));

export type ApiError = z.infer<typeof ErrorResponse>;
