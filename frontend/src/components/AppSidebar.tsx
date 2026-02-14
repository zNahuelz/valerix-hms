import { useState } from 'react';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from './ui/dropdown-menu';
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarGroup,
  SidebarGroupLabel,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuAction,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarMenuSub,
  SidebarMenuSubButton,
  SidebarMenuSubItem,
  SidebarSeparator,
  useSidebar,
} from './ui/sidebar';
import {
  RiArrowDropDownFill,
  RiArrowRightSLine,
  RiExpandUpDownLine,
  RiHome4Line,
  RiLogoutCircleLine,
  RiMapPinUserFill,
  RiNotification3Line,
  RiRestaurantLine,
} from '@remixicon/react';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from './ui/collapsible';
import { Avatar, AvatarFallback, AvatarImage } from './ui/avatar';
import ModeToggle from './ModeToggle';
import { useLoaderData } from 'react-router';
import { useAuth } from '@/context/AuthContext';
import { useTranslation } from 'react-i18next';
import SidebarItems from './SidebarItems';

export default function AppSidebar({ ...props }: React.ComponentProps<typeof Sidebar>) {
  const { user, logout } = useAuth();
  const { t } = useTranslation();
  const teams = [
    {
      name: user?.clinic?.name, //TODO...!
      logo: RiMapPinUserFill,
      plan: 'Plan 1',
    },
  ];

  const [activeTeam, setActiveTeam] = useState(teams[0]);

  if (!activeTeam) {
    return null;
  }

  const { isMobile } = useSidebar();

  return (
    <Sidebar variant='inset' {...props}>
      <SidebarHeader>
        <SidebarMenu>
          <SidebarMenuItem>
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <SidebarMenuButton className='w-fit px-1.5'>
                  <div className='bg-sidebar-primary text-sidebar-primary-foreground flex aspect-square size-5 items-center justify-center rounded-md'>
                    <activeTeam.logo className='size-3' />
                  </div>
                  <span className='truncate font-medium'>{activeTeam.name}</span>
                  <RiArrowDropDownFill className='opacity-50' />
                </SidebarMenuButton>
              </DropdownMenuTrigger>
              <DropdownMenuContent
                className='w-64 rounded-lg'
                align='start'
                side='bottom'
                sideOffset={4}
              >
                <DropdownMenuLabel className='text-muted-foreground text-xs'>
                  Teams
                </DropdownMenuLabel>
                {teams.map((team) => (
                  <DropdownMenuItem
                    key={team.name}
                    onClick={() => setActiveTeam(team)}
                    className='gap-2 p-2'
                  >
                    <div className='flex size-6 items-center justify-center rounded-xs border'>
                      <team.logo className='size-4 shrink-0' />
                    </div>
                    {team.name}
                  </DropdownMenuItem>
                ))}
              </DropdownMenuContent>
            </DropdownMenu>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarHeader>
      <SidebarContent>
        <SidebarItems></SidebarItems>
      </SidebarContent>
      <SidebarFooter>
        <SidebarMenu>
          <SidebarMenuItem className='mb-2'>
            <ModeToggle></ModeToggle>
          </SidebarMenuItem>
          <SidebarMenuItem>
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <SidebarMenuButton
                  size='lg'
                  className='data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground'
                >
                  <Avatar className='h-8 w-8 rounded-lg'>
                    <AvatarImage src={user?.avatar || ''} alt={user?.username} />
                    <AvatarFallback className='rounded-lg uppercase'>{`${user?.username[0]}${user?.username[1]}`}</AvatarFallback>
                  </Avatar>
                  <div className='grid flex-1 text-left text-sm leading-tight'>
                    <span className='truncate font-medium'>{user?.username}</span>
                    <span className='truncate text-xs'>{user?.email}</span>
                  </div>
                  <RiExpandUpDownLine className='ml-auto size-4' />
                </SidebarMenuButton>
              </DropdownMenuTrigger>
              <DropdownMenuContent
                className='w-(--radix-dropdown-menu-trigger-width) min-w-56 rounded-lg'
                side={isMobile ? 'bottom' : 'right'}
                align='end'
                sideOffset={4}
              >
                <DropdownMenuLabel className='p-0 font-normal'>
                  <div className='flex items-center gap-2 px-1 py-1.5 text-left text-sm'>
                    <Avatar className='h-8 w-8 rounded-lg'>
                      <AvatarImage src={user?.avatar || ''} alt={user?.username} />
                      <AvatarFallback className='rounded-lg uppercase'>{`${user?.username[0]}${user?.username[1]}`}</AvatarFallback>
                    </Avatar>
                    <div className='grid flex-1 text-left text-sm leading-tight'>
                      <span className='truncate font-medium'>{user?.username}</span>
                      <span className='truncate text-xs'>{user?.email}</span>
                    </div>
                  </div>
                </DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuGroup>
                  <DropdownMenuItem>
                    <RiNotification3Line />
                    Notifications
                  </DropdownMenuItem>
                </DropdownMenuGroup>
                <DropdownMenuSeparator />
                <DropdownMenuItem onClick={() => logout()}>
                  <RiLogoutCircleLine />
                  {t('auth.logout')}
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarFooter>
    </Sidebar>
  );
}
