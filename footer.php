```php
	</div><!-- #content -->

	<footer class="bg-gray-50 py-16 px-4 sm:px-6 lg:px-8">
		<div class="max-w-7xl mx-auto">
			<!-- Top section with two cards -->
			<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-16">
				<!-- Left Card - Purple Button -->
				<div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-gray-900 to-gray-800 p-8 sm:p-12 min-h-[300px] flex flex-col justify-between">
					<div class="absolute inset-0 opacity-20">
						<?php if (get_theme_mod('footer_card_left_bg_image')) : ?>
							<img src="<?php echo esc_url(get_theme_mod('footer_card_left_bg_image')); ?>" alt="<?php echo esc_attr__('Background', 'starapple'); ?>" class="w-full h-full object-cover">
						<?php else : ?>
							<img src="https://images.unsplash.com/photo-1613323593608-abc90fec84ff?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w4MTAzMDV8MHwxfHNlYXJjaHwyfHxpbWFnZXxlbnwwfDB8fHwxNzU5NjYzODYwfDA&ixlib=rb-4.1.0&q=80&w=1080&w=800&h=600&fit=crop" alt="<?php echo esc_attr__('Background', 'starapple'); ?>" class="w-full h-full object-cover">
						<?php endif; ?>
					</div>
					<div class="relative z-10">
						<p class="text-white text-center text-lg sm:text-xl leading-relaxed mb-8">
							<?php echo esc_html(get_theme_mod('footer_card_left_text', __('StarApple vind je uitdagende projecten die bij je passen — bij toonaangevende opdrachtgevers in overheid, corporate en tech.', 'starapple'))); ?>
						</p>
						<div class="flex justify-center">
							<a href="<?php echo esc_url(get_theme_mod('footer_card_left_button_url', '#')); ?>" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-medium px-10 py-4 rounded-full transition-all duration-300 transform hover:scale-105 shadow-lg">
								<?php echo esc_html(get_theme_mod('footer_card_left_button_text', __('Lees meer', 'starapple'))); ?>
							</a>
						</div>
					</div>
				</div>

				<!-- Right Card - Green Button -->
				<div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-gray-900 to-gray-800 p-8 sm:p-12 min-h-[300px] flex flex-col justify-between">
					<div class="absolute inset-0 opacity-20">
						<?php if (get_theme_mod('footer_card_right_bg_image')) : ?>
							<img src="<?php echo esc_url(get_theme_mod('footer_card_right_bg_image')); ?>" alt="<?php echo esc_attr__('Background', 'starapple'); ?>" class="w-full h-full object-cover">
						<?php else : ?>
							<img src="https://images.unsplash.com/photo-1613323593608-abc90fec84ff?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w4MTAzMDV8MHwxfHNlYXJjaHwyfHxpbWFnZXxlbnwwfDB8fHwxNzU5NjYzODYwfDA&ixlib=rb-4.1.0&q=80&w=1080&w=800&h=600&fit=crop" alt="<?php echo esc_attr__('Background', 'starapple'); ?>" class="w-full h-full object-cover">
						<?php endif; ?>
					</div>
					<div class="relative z-10">
						<p class="text-white text-center text-lg sm:text-xl leading-relaxed mb-8">
							<?php echo esc_html(get_theme_mod('footer_card_right_text', __('StarApple levert snel de juiste professional — met diepgaande marktkennis en een persoonlijke aanpak.', 'starapple'))); ?>
						</p>
						<div class="flex justify-center">
							<a href="<?php echo esc_url(get_theme_mod('footer_card_right_button_url', '#')); ?>" class="inline-block bg-green-400 hover:bg-green-500 text-gray-900 font-medium px-10 py-4 rounded-full transition-all duration-300 transform hover:scale-105 shadow-lg">
								<?php echo esc_html(get_theme_mod('footer_card_right_button_text', __('Lees meer', 'starapple'))); ?>
							</a>
						</div>
					</div>
				</div>
			</div>

			<!-- Bottom section with text and image -->
			<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
				<!-- Left Content -->
				<div class="space-y-6">
					<h3 class="text-purple-600 text-lg font-medium">
						<?php echo esc_html(get_theme_mod('footer_about_subtitle', __('Dit is Starapple:', 'starapple'))); ?>
					</h3>
					<h2 class="text-4xl sm:text-5xl font-bold text-gray-900 leading-tight">
						<?php echo esc_html(get_theme_mod('footer_about_title', __('De specialist achter de match', 'starapple'))); ?>
					</h2>
					<p class="text-gray-600 text-lg leading-relaxed">
						<?php echo esc_html(get_theme_mod('footer_about_description', __('StarApple is geen standaard bemiddelaar. Bij ons draait het om de perfecte interim match. Wij combineren inhoudelijke IT-expertise met een persoonlijke, gedreven aanpak. Of je nu zoekt naar de juiste interim opdracht of tijdelijke versterking, bij ons weet je precies wie je aan de lijn hebt en waar je aan toe bent. Leer ons kennen.', 'starapple'))); ?>
					</p>
					<div class="pt-4">
						<a href="<?php echo esc_url(get_theme_mod('footer_about_button_url', '#')); ?>" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-medium px-10 py-4 rounded-full transition-all duration-300 transform hover:scale-105 shadow-lg">
							<?php echo esc_html(get_theme_mod('footer_about_button_text', __('Over ons', 'starapple'))); ?>
						</a>
					</div>
				</div>

				<!-- Right Image -->
				<div class="relative rounded-3xl overflow-hidden shadow-2xl">
					<?php if (get_theme_mod('footer_about_image')) : ?>
						<img src="<?php echo esc_url(get_theme_mod('footer_about_image')); ?>" alt="<?php echo esc_attr__('Team collaboration', 'starapple'); ?>" class="w-full h-full object-cover min-h-[400px]">
					<?php else : ?>
						<img src="https://images.unsplash.com/photo-1613323593608-abc90fec84ff?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w4MTAzMDV8MHwxfHNlYXJjaHwyfHxpbWFnZXxlbnwwfDB8fHwxNzU5NjYzODYwfDA&ixlib=rb-4.1.0&q=80&w=1080&w=1200&h=800&fit=crop" alt="<?php echo esc_attr__('Team collaboration', 'starapple'); ?>" class="w-full h-full object-cover min-h-[400px]">
					<?php endif; ?>
				</div>
			</div>

			<?php if (has_nav_menu('footer')) : ?>
			<div class="mt-16 pt-8 border-t border-gray-200">
				<?php
				wp_nav_menu(array(
					'theme_location' => 'footer',
					'menu_id'        => 'footer-menu',
					'container'      => 'nav',
					'container_class' => 'footer-navigation',
					'menu_class'     => 'footer-nav-menu',
					'walker'         => class_exists('TF_Footer_Nav_Walker') ? new TF_Footer_Nav_Walker() : '',
					'fallback_cb'    => false,
					'depth'          => 1,
				));
				?>
			</div>
			<?php endif; ?>

			<div class="mt-8 pt-8 border-t border-gray-200 text-center text-gray-600 text-sm">
				<p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php echo esc_html__('All rights reserved.', 'starapple'); ?></p>
			</div>
		</div>
	</footer>

	<?php wp_footer(); ?>

</body>
</html>
```