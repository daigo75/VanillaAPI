<!DOCTYPE html>
<html>
<head>
   {asset name="Head"}
</head>
<body id="{$BodyID}" class="{$BodyClass}">

   {assign var=Repo value="https://github.com/kasperisager/VanillaAPI"}
   {assign var=GhbBtns value="http://ghbtns.com/github-btn.html?user=kasperisager"}

   <div class="navbar navbar-static-top">
      <div class="navbar-inner">
         <div class="container">
            <a class="brand" href="{link path="api"}">Vanilla API</a>
            <ul class="nav">
               <li>{link path="api" text="Explorer"}</li>
               <li class="dropdown">
                  {link format="<a href='#' data-toggle='dropdown' class='%class'>%text <b class='caret'></b></a>" class="dropdown-toggle" path="api/wiki" text="Documentation"}
                  <ul class="dropdown-menu">
                     <li>
                        {link path="api/wiki" text="Index"}
                     </li>
                     <li class="divider"></li>
                     <li class="nav-header">Documentation</li>
                     <li>{link path="api/wiki/installation" text="Installation"}</li>
                     <li>{link path="api/wiki/annotations" text="Annotations"}</li>
                     <li>{link path="api/wiki/extending" text="Extending"}</li>
                  </ul>
               </li>
            </ul>
            <ul class="nav pull-right">
               <embed src="{$GhbBtns}&repo=VanillaAPI&type=watch&count=true" width="85" height="20">
               <embed src="{$GhbBtns}&repo=VanillaAPI&type=fork&count=true" width="90" height="20">
               <embed src="{$GhbBtns}&type=follow&count=true" width="180" height="20">
            </ul>
         </div>
      </div>
   </div>

   <div class="container">
      {asset name="Content"}
   </div>

   {asset name="Foot"}
   {event name="AfterBody"}
</body>
</html>