<h1>Welcome</h1>
<ul>
    <li><a href="/admin/exhibitions">Exhibitions</a></li>
    <li><a href="/admin/artists">Artists</a></li>
    <li><a href="/admin/images">Images</a></li>
    <li><a href="/admin/news">News</a></li>
    <?php if (!empty($user) && $user["superuser"]): ?>
    <li><a href="/admin/staff">Staff</a></li>
    <li><a href="/admin/users">Users</a></li>
    <?php endif; ?>
</ul>