# Cherry Testimonials
A testimonials management plugin for WordPress.
Сompatibility: *Cherry Framewokr v.4+*

##Change log##

#### v1.1.1 ####

* FIX: stop using `popuplinks` function (deprecated) - WordPress 4.5 compatibility
* FIX: wpml-plugin compatibility
* UPD: functions comment
* UPD: languages files
* UPD: file headers

#### v1.1.0 ####

* NEW: Custom fields in metabox - `position`, `company name`
* ADD: Pass template name into testimonials block wrapper CSS classes
* UPD: Minify CSS
* UPD: Translated string

#### v1.0.2 ####

* UPD: Optimize a shortcode registration
* UPD: Optimize conditional rule for a `pre_get_posts` filter
* UPD: Text to translate
* UPD: Refactor `Cherry_Testimonials_Data` class
* UPD: Templates file - added a hooks
* ADD: `Cherry_Testimonials_Template_Callbacks` class - macros-handler
* ADD: Macros-button to the Shortcodes Tempalter (swiper_carousel)

#### v1.0.1 ####

* ADD: Compatibility for wpml-plugin
* UPD: Avatar size on testi page
* UPD: Updater logic

## Features
* CPT Testimonials
* Page template (named Testimonials) + single template
* Widget (named Cherry Testimonials Widget)
* Shortcode (named cherry_testimonials)
* 20+ custom hooks
* Translation (Localization)

## How to use

#### In a post/page
Insert a shortcode `[cherry_testimonials]` to the post/page content.

#### In a page template
Fire the action *'cherry_get_testimonials'*. Example:
```
	do_action( 'cherry_get_testimonials' );
```

#### In a sidebar
Just drop widget to the your sidebar.

## Help
Found a bug? Feature requests? [Create an issue - Thanks!](https://github.com/CherryFramework/cherry-testimonials/issues/new)