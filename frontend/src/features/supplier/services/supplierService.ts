import { z } from 'zod';
import { BaseService } from '@/lib/baseService';
import { SupplierSchema } from '@/schemas/supplier';
import { http } from '@/lib/httpWrapper';

export interface SupplierQuery {
  id?: number;
  name?: string;
  ruc?: string;
  email?: string;
  trashed?: boolean;
  sortBy?: 'id' | 'name' | 'ruc' | 'created_at';
  sortDir?: 'asc' | 'desc';
  page?: number;
  limit?: number;
}

class SupplierService extends BaseService<typeof SupplierSchema, SupplierQuery> {
  constructor() {
    super(http, 'supplier', SupplierSchema, {
      sortBy: 'sort_by',
      sortDir: 'sort_dir',
      limit: 'per_page',
    });
  }
}

export const supplierService = new SupplierService();
