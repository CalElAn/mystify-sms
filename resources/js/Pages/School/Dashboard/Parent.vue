<template>
  <section
    class="flex items-center justify-start gap-4 text-4xl font-semibold tracking-wide"
  >
    Children
    <Menu as="div" class="base-card relative text-base">
      <MenuButton class="w-full p-2">
        <DotsVerticalIcon
          class="h-5 w-5 text-purple-600 transition-transform hover:scale-110"
        />
      </MenuButton>
      <MenuItemsTransition>
        <MenuItems class="menu-items right-0 z-10 mt-2 w-max origin-top-right">
          <div class="px-1 py-1">
            <MenuItem as="div" v-slot="{ active }">
              <MenuItemButton :active="active"> Add child </MenuItemButton>
            </MenuItem>
            <MenuItem as="div" v-slot="{ active }">
              <MenuItemButton :active="active"> Remove child </MenuItemButton>
            </MenuItem>
          </div>
        </MenuItems>
      </MenuItemsTransition>
    </Menu>
  </section>
  <section class="grid mt-6 mb-auto grid-cols-3">
    <button class="base-card border-white hover:border-purple-600 border flex gap-6 p-4">
      <ProfilePicture
        :profilePicturePath="$page.props.auth.user.profile_picture_path"
        widthClass="w-1/4"
        heightClass="h-full"
      />
      <div class="flex flex-col gap-2">
        <div class="text-xl text-fuchsia-600 font-semibold tracking-wide">
          {{ $page.props.auth.user.name }}
        </div>
        <div>
          <a
            class="decoration-purple-600 hover:underline"
            :href="'tel:' + $page.props.auth.user.phone_number"
            >{{ $page.props.auth.user.phone_number }}</a
          >
        </div>
        <div>
          <a
            class="decoration-purple-600 hover:underline"
            :href="'mailto:' + $page.props.auth.user.email"
          >
            {{ $page.props.auth.user.email }}
          </a>
        </div>
      </div>
    </button>
  </section>
</template>

<script setup></script>

<style lang="scss" scoped></style>
