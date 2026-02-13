import { PAGINATION_LIMITS } from '@/constants/arrays';
import { Field, FieldLabel } from './ui/field';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from './ui/select';
import {
  Pagination,
  PaginationContent,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious,
} from './ui/pagination';
import { useTranslation } from 'react-i18next';

type Props = {
  page: number;
  limit: number;
  totalPages: number;
  onPageChange: (page: number) => void;
  onLimitChange: (page: number) => void;
  limits?: { label: string; value: number }[];
  entityCounter?: string;
};

export default function AppPaginator({
  page,
  limit,
  totalPages,
  onPageChange,
  onLimitChange,
  limits = PAGINATION_LIMITS,
  entityCounter,
  ...props
}: React.ComponentProps<'div'> & Props) {
  const { t } = useTranslation();

  const goToPage = (p: number) => {
    if (p >= 1 && p <= totalPages && p !== page) onPageChange(p);
  };

  const isFirstPage = page <= 1;
  const isLastPage = page >= totalPages;

  return (
    <div className='flex items-center justify-between gap-4' {...props}>
      <Field orientation='horizontal' className='w-fit'>
        <FieldLabel htmlFor='select-rows-per-page'>{t('common.rowsPerPage')}</FieldLabel>
        <Select
          defaultValue={String(limits[1].value)}
          onValueChange={(e) => onLimitChange(Number(e))}
        >
          <SelectTrigger className='w-20' id='select-rows-per-page'>
            <SelectValue />
          </SelectTrigger>
          <SelectContent align='start'>
            <SelectGroup>
              {limits.map((e) => (
                <SelectItem value={String(e.value)} key={e.value}>
                  {e.label}
                </SelectItem>
              ))}
            </SelectGroup>
          </SelectContent>
        </Select>
      </Field>
      <span className='hidden sm:inline text-sm'>{entityCounter}</span>
      <Pagination className='mx-0 w-auto'>
        <PaginationContent>
          <PaginationItem>
            <PaginationPrevious
              onClick={() => !isFirstPage && goToPage(page - 1)}
              text={t('common.previous')}
              className={isFirstPage ? 'pointer-events-none opacity-50' : ''}
              aria-disabled={isFirstPage}
            />
          </PaginationItem>
          <PaginationItem>
            <PaginationLink
              onClick={() => {
                if (page !== 1) onPageChange(1);
              }}
            >
              {page}
            </PaginationLink>
          </PaginationItem>
          <PaginationItem>
            <PaginationNext
              onClick={() => !isLastPage && goToPage(page + 1)}
              text={t('common.next')}
              className={isLastPage ? 'pointer-events-none opacity-50' : ''}
              aria-disabled={isLastPage}
            />
          </PaginationItem>
        </PaginationContent>
      </Pagination>
    </div>
  );
}
