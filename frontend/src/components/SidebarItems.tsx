import {
  SidebarGroup,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarMenuSub,
  SidebarMenuSubButton,
  SidebarMenuSubItem,
} from './ui/sidebar';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from './ui/collapsible';
import { RiArrowRightSLine, RiBuilding2Line, RiHome4Line } from '@remixicon/react';
import { useTranslation } from 'react-i18next';
import { NavLink } from 'react-router';

export default function SidebarItems() {
  const { t } = useTranslation();
  return (
    <SidebarGroup>
      <SidebarGroupLabel>{t('common.module_other')}</SidebarGroupLabel>
      <SidebarMenu>
        <SidebarMenuItem>
          <NavLink to='/dashboard'>
            <SidebarMenuButton>
              <RiHome4Line />
              {t('common.home')}
            </SidebarMenuButton>
          </NavLink>
        </SidebarMenuItem>

        <Collapsible asChild className='group/collapsible'>
          <SidebarMenuItem>
            <CollapsibleTrigger asChild>
              <SidebarMenuButton>
                <RiBuilding2Line />
                <span>{t('supplier.supplier_other')}</span>
                <RiArrowRightSLine className='ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90' />
              </SidebarMenuButton>
            </CollapsibleTrigger>
            <CollapsibleContent>
              <SidebarMenuSub>
                <SidebarMenuSubItem>
                  <SidebarMenuSubButton asChild>
                    <NavLink to='/dashboard/supplier'>{t('common.index')}</NavLink>
                  </SidebarMenuSubButton>
                </SidebarMenuSubItem>
                <SidebarMenuSubItem>
                  <SidebarMenuSubButton asChild>
                    <NavLink to='/dashboard/supplier/store'>{t('common.store')}</NavLink>
                  </SidebarMenuSubButton>
                </SidebarMenuSubItem>
              </SidebarMenuSub>
            </CollapsibleContent>
          </SidebarMenuItem>
        </Collapsible>
      </SidebarMenu>
    </SidebarGroup>
  );
}
