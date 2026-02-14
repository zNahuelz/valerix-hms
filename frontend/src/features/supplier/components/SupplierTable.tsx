import type { Column } from '@/components/AppTable';
import AppTable from '@/components/AppTable';
import type { Supplier } from '@/schemas/supplier';
import type { ReactNode } from 'react';
import { useTranslation } from 'react-i18next';

type Props = {
  data: Supplier[];
  actions?: (row: Supplier) => ReactNode;
  fetchFailed?: boolean;
};

export default function SupplierTable({
  data,
  actions,
  fetchFailed = false,
  ...props
}: React.ComponentProps<'div'> & Props) {
  const { t } = useTranslation();
  const columns = [
    { key: 'id', label: t('common.idAlt') },
    { key: 'name', label: t('common.name_one') },
    { key: 'ruc', label: t('common.ruc').toUpperCase() },
    {
      key: 'phone',
      label: t('common.phone'),
      render: (row) => row.phone || '-----',
    },
    {
      key: 'manager',
      label: t('common.manager_one'),
      render: (row) => `${row.manager ?? '-----'}`,
    },
    { key: 'deletedAt', label: t('common.status') },
    { key: 'updatedAt', label: t('common.updatedAt') },
  ] satisfies Column<Supplier>[];

  return (
    <AppTable
      columns={columns}
      data={data}
      actions={actions}
      error={fetchFailed ? 'ERROR' : 'NO ERROR'}
      hideActions={true}
      {...props}
    ></AppTable>
  );
}
