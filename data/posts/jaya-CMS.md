
## Layers

It's built on a custom file-based CMS written in only PHP. It uses the directory structure and filenames to determine the names of posts. For example, this text is stored in a file called `jaya-CMS.md` - the filename is then transformed into CamelCase for the title of the post. The date is determined by the modified-time of the file.

## Modules

The modules are slottable (soon to be managed automatically by a git shim) and are parsed using a combination of the file directory tree and JSON files. As an example, this is the module.json file for the whole site:

```javascript
{
	"name" : "james",
	"dependencies" : [
		"extern.Bootstrap",
		"extern.jQuery",
		"posts"
	],
	"stylesheets" : [
		"style.less"
	],
	"views" : {
		"main" : "views/main.php"
	}
}
```

It includes several dependencies, including two external deps, and one that I wrote to display these very Markdown posts. When a URL is requested, the app finds the view that corresponds to the view (in this case `/` goes to `james.main`). The a View object is created, using the `modules/james/views/main.php` template file and it is parsed and cached.

The view may reference other views by putting it in a handle-bars tag `view:"example.view"` where `example.view` is a generated module path to a view, defined in the module `views` index, as is done with `"views/main.php"` in the file above.

## Availabilty

It is available for view on it's GitHub <a href="http://www.github.com/etskh/jaya-cms" target="_blank"_>page</a>.

## Why the Weird Name?

It's named after my <a href="https://instagram.com/p/20-O40kKsC/" target="_blank"_>friend's dog</a>. She is the cutest entity in all the galaxy and didn't have anything named after her yet.

<div class="tags">CMS,Jaya,Jaya-CMS,web,json,markdown,php</div>
