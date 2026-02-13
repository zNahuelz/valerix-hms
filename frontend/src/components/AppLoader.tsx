import { useTranslation } from 'react-i18next';
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle } from './ui/empty';
import { Spinner } from './ui/spinner';

interface Props {
  message?: string;
}

export default function AppLoader({ message, ...props }: React.ComponentProps<'div'> & Props) {
  const { t } = useTranslation();
  return (
    <Empty className='w-full' {...props}>
      <EmptyHeader>
        <Spinner className='size-20 text-primary dark:text-white' />
        <EmptyTitle className='text-xl'>{message ?? t('common.loading')}</EmptyTitle>
      </EmptyHeader>
    </Empty>
  );
}
