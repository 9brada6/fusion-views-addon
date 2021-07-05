By default, all the metas are hold in a post meta individually. I choose this because:
- They are fetched by default when a post(single page) is shown, so no additional database calls.
- We can easily retrieve the order of posts by total_views/daily_views if needed(To display most viewed posts).
- If we want to register users by {hash_ip, date, post_id} views in a table, for statistic data in the future, these meta are still needed.

I didn't tried figure out how to use dynamic_css(), so I just put in render a <style></style>. I know it's wrong, but also maybe dynamic_css() function isn't for echoeing <style>? I didn't try to see exactly what that function is tying to do tho.

I don't understand what settings_to_params do, it seems to work the same without. Maybe is that button right top that goes to the same global setting?

I couldn't find a way to put placeholders on padding setting(but I didn't tried very hard).

I know that I didn't sanitize the variables.

I don't know how to put the icon in the avada classic buider.

What does 'remove_from_atts' do?

I have added the separator in the first phase, but I don't think it's worth it because:
a) I don't think that a lof of people needs it.
b) It requires some hacks to do, a lot of code, a lot of if's and checks, a lot of CSS.
c) It's not supported by CSS by default, like a text-decoration for example.
