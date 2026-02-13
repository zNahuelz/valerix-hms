import { Helmet } from '@dr.pogodin/react-helmet';
import { useTranslation } from 'react-i18next';

export default function SupplierStoreView() {
  const { t } = useTranslation();
  return (
    <>
      <Helmet>
        <title>{t('views.supplier.store')}</title>
      </Helmet>
      <div>Supplier Store!</div>
    </>
  );
}
