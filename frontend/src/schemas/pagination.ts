import { z } from 'zod';

export const PaginationMetaSchema = z
  .object({
    current_page: z.number(),
    last_page: z.number(),
    per_page: z.number(),
    total: z.number(),
    from: z.number().nullable(),
    to: z.number().nullable(),
  })
  .transform((m) => ({
    currentPage: m.current_page,
    lastPage: m.last_page,
    perPage: m.per_page,
    total: m.total,
  }));

export type PaginationMeta = z.output<typeof PaginationMetaSchema>;

export const PaginatedResponseSchema = <T extends z.ZodTypeAny>(dataSchema: T) =>
  z.object({
    data: z.array(dataSchema),
    meta: PaginationMetaSchema,
  });
