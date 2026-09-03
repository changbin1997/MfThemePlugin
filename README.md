## 插件功能

这是 Typecho 主题 [MwordStar](https://www.misterma.com/archives/812/) 和 [Facile](https://www.misterma.com/archives/899/) 的辅助插件。

我的两个 Typecho 主题都有一个私密评论的功能，发布的评论只有评论者和网站管理员可以看到评论内容。

Typecho 有一个评论 RSS，可以通过 RSS 工具查看最新的评论，在 RSS 页面，任何人都可以看到私密评论。

由于 RSS 页面不会加载主题，所以无法通过主题控制 RSS 页面的内容。

这个插件的主要功能就是用来隐藏评论 RSS 页面的私密评论，私密评论会显示为 `私密评论，仅评论者和管理员可见` 。

这就是插件目前的功能，后续一些不方便通过主题实现的功能，可能也会放到插件里。

**如果你关闭了 RSS 功能，可以不需要这个插件。**

注意，主题设置里隐藏 RSS 入口不会关闭 RSS，关闭 RSS 只能修改 Typecho 文件或直接在 Web 服务器设置屏蔽。

## 安装和使用

在 Github 仓库页面 https://github.com/changbin1997/MfThemePlugin 点击右上方的 `Code` ，选择 `Download ZIP` ，下载完成后是一个 zip 的压缩包。

把下载的压缩包拷贝到 Typecho 目录的 `usr/plugins` 目录，解压到当前文件夹，解压后应该会有一个 `MfThemePlugin-main` 的目录，把 `MfThemePlugin-main` 目录重命名为 `MfThemePlugin` 。

登录 Typecho 后台，在顶部 `控制台` 菜单选择 `插件` ，进入插件管理页面可以看到 `MfThemePlugin` ，选择 `启用` 。

