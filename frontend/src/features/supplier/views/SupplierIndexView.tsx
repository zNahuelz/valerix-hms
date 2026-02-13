import AppLoader from '@/components/AppLoader';
import { Helmet } from '@dr.pogodin/react-helmet';
import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { type SupplierQuery, supplierService } from '../services/supplierService';
import type { Supplier } from '@/schemas/supplier';
import SupplierTable from '../components/SupplierTable';
import AppPaginator from '@/components/AppPaginator';

export default function SupplierIndexView() {
  //TODO: Continue...!
  const { t } = useTranslation();
  const [isLoading, setIsLoading] = useState(false);
  const [suppliers, setSuppliers] = useState<Supplier[]>([]);
  const [totalPages, setTotalPages] = useState(1);
  const [totalItems, setTotalItems] = useState(0);
  const [query, setQuery] = useState<SupplierQuery>({
    page: 1,
    limit: 20,
    trashed: false,
  });

  const fetchSuppliers = async (q: SupplierQuery) => {
    setIsLoading(true);
    try {
      const { data, meta } = await supplierService.index(q);
      setSuppliers(data);
      setTotalPages(meta.lastPage);
      setTotalItems(meta.total);
    } catch (error) {
      console.log(error);
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchSuppliers(query);
  }, [query]);

  return (
    <>
      <Helmet>
        <title>{t('views.supplier.index')}</title>
      </Helmet>
      {isLoading ? (
        <AppLoader></AppLoader>
      ) : (
        <SupplierTable data={suppliers} fetchFailed={false}></SupplierTable>
      )}
      <AppPaginator
        page={query.page!}
        limit={query.limit!}
        totalPages={totalPages}
        onPageChange={(page) => setQuery((q) => ({ ...q, page }))}
        onLimitChange={(limit) => setQuery((q) => ({ ...q, limit, page: 1 }))}
        entityCounter={
          suppliers.length >= 1 && !isLoading
            ? t('supplier.supplierCounter', {
                count: suppliers.length,
                total: totalItems,
              })
            : ''
        }
      ></AppPaginator>
    </>
  );
}
