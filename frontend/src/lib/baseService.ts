import { PaginatedResponseSchema, type PaginationMeta } from '@/schemas/pagination';
import type { http as _http } from './httpWrapper';
import { z } from 'zod';

export class BaseService<
  TSchema extends z.ZodTypeAny,
  TQuery extends Record<string, any> = Record<string, any>,
> {
  constructor(
    protected http: typeof _http,
    protected resource: string,
    protected schema: TSchema,
    protected paramMap: Record<string, string> = {}
  ) {}

  /**
   * GET /resource
   */
  async index(query: TQuery): Promise<{ data: z.output<TSchema>[]; meta: PaginationMeta }> {
    const params = new URLSearchParams();

    for (const [key, value] of Object.entries(query)) {
      if (value == null) continue;

      const mappedKey = this.paramMap[key] ?? key;
      let formattedValue: string;

      if (typeof value === 'boolean') {
        formattedValue = value ? '1' : '0';
      } else {
        formattedValue = String(value);
      }

      params.set(mappedKey, formattedValue);
    }

    const raw = await this.http.get(this.resource, { searchParams: params }).json();
    return PaginatedResponseSchema(this.schema).parse(raw);
  }

  /**
   * GET /resource/:id
   */
  async show(id: number | string): Promise<z.output<TSchema>> {
    const data = await this.http.get(`${this.resource}/${id}`).json();
    return this.schema.parse(data);
  }

  /**
   * POST /resource
   */
  async create(payload: Partial<z.input<TSchema>>): Promise<z.output<TSchema>> {
    const data = await this.http.post(this.resource, { json: payload }).json();
    return this.schema.parse(data);
  }

  /**
   * PUT /resource/:id
   */
  async update(
    id: number | string,
    payload: Partial<z.input<TSchema>>
  ): Promise<z.output<TSchema>> {
    const data = await this.http.put(`${this.resource}/${id}`, { json: payload }).json();
    return this.schema.parse(data);
  }

  /**
   * DELETE /resource/:id
   */
  async destroy(id: number | string): Promise<void> {
    await this.http.delete(`${this.resource}/${id}`);
  }
}
