import { Helmet } from '@dr.pogodin/react-helmet';
import { useTranslation } from 'react-i18next';

export default function SupplierUpdateView() {
  const { t } = useTranslation();
  return (
    <>
      <Helmet>
        <title>{t('views.supplier.update')}</title>
      </Helmet>
      <div>Supplier Update!</div>
    </>
  );
}
