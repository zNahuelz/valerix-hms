import type { ReactNode } from 'react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from './ui/table';
import { useTranslation } from 'react-i18next';
import { formatAsDatetime } from '@/lib/format';

export type Column<T> = {
  key: keyof T;
  label: string;
  render?: (row: T) => ReactNode;
};

export type Props<T> = {
  columns: Column<T>[];
  data: T[];
  error?: string;
  actions?: (row: T) => ReactNode;
  hideActions?: boolean;
};

export default function AppTable<T>({
  columns,
  data,
  error,
  actions,
  hideActions = false,
  ...props
}: React.ComponentProps<'div'> & Props<T>) {
  const { t } = useTranslation();
  return (
    <div {...props} className='border rounded-sm mb-2'>
      <Table>
        <TableHeader>
          <TableRow>
            {columns.map((col) => (
              <TableHead key={String(col.key)}>{col.label}</TableHead>
            ))}
            {!hideActions && (
              <TableHead className='text-right'>{t('common.action_other')}</TableHead>
            )}
          </TableRow>
        </TableHeader>
        <TableBody>
          {data?.map((row, i) => (
            <TableRow key={i}>
              {columns.map((col) => {
                const value = row[col.key];
                if (col.key === 'createdAt' || col.key === 'updatedAt') {
                  return <TableCell key={String(col.key)}>{formatAsDatetime(value)}</TableCell>;
                }
                if (col.key === 'deletedAt') {
                  const disabled = Boolean(value);
                  return (
                    <TableCell
                      className={`uppercase font-bold text-primary ${disabled ? 'text-destructive' : ''}`}
                      key={String(col.key)}
                    >
                      {disabled ? t('common.disabledEntity') : t('common.enabledEntity')}
                    </TableCell>
                  );
                }
                return <TableCell key={String(col.key)}>{String(value)}</TableCell>;
              })}

              {!hideActions && (
                <TableCell className='text-right'>{actions && actions(row)}</TableCell>
              )}
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </div>
  );
}
