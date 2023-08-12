# WP Media Crawler - Explanation

## The problem to be solved

From a business perspective, the challenge lies in establishing a method to assess the well-being of the website's backlinks, focusing solely on the homepage for this evaluation. By examining the link landscape, the site administrator can identify opportunities to enhance their website's SEO and boost page views.

Looking from an engineering standpoint, the challenge revolves around ensuring a positive user experience for the administrator during the analysis of the page's links. Equally vital is guaranteeing that the administrator examines the most recent links, necessitating up-to-date data. Additionally, leveraging the capabilities of programming languages could facilitate filtering and delivering the most crucial links for the analysis.


# A technical specification outlining my approach to resolving the issue entails:


Considering the capabilities of WordPress and its array of tools and packages, the most optimal strategy involves crafting a plugin. In terms of technical requisites, the initial phase necessitates creating a Proof of Concept (PoC) to ascertain the most suitable crawler engine. This particular aspect is where uncertainty lies in determining the optimal approach.

Subsequently, having settled on the crawler engine, the subsequent step entails shaping the definitive engine architecture, followed by the comprehensive development of the plugin itself. My customary practice involves employing Object-Oriented Programming (OOP) to construct and validate individual components of the system autonomously. The ultimate synthesis of all components, culminating in a functioning whole, is a deeply gratifying hallmark of the Software Engineering process.


## The rationale behind your technical choices and the reasons behind them.

Following my outlined plan, I commenced by crafting a Proof of Concept (PoC) for the crawler engine. Subsequently, I structured the entire system around this foundation. Embracing Domain Driven Development (DDD) principles, I aimed to compartmentalize each "context" into discrete packages, orchestrating their execution through a procedural initializer – often via tasks like cron jobs or the admin handler.

During the PoC phase, I pre-emptively established a repository and bestowed upon it a name reflective of its inception. This nomenclature persisted in my decision-making process. Drawing upon my background as a seasoned software engineer, I acknowledge the significance of meaningful designations. Conversely, pondering the innovation within WP Media products, I contemplated an alternative appellation, such as "WP Media SEO," igniting a hint of anticipation for a future of remarkable achievements.

### The engine responsible for crawling.

Having conducted an extensive exploration to identify optimal strategies for link crawling within the PHP/WordPress environment, my selection gravitated towards leveraging the Symfony Crawler. Renowned for its widespread adoption and intuitive nature, this library emerges as a front-runner. While the native PHP DomDocument class stands as an alternative, I am inclined towards the Symfony Crawler due to my familiarity with it and the desire to circumvent its constraints and idiosyncrasies, a sentiment shaped by past experiences.

### Determining the Suitable Location for Storing the sitemap.html File

Initially, my consideration led me to contemplate storing the sitemap.html file either within the WP root directory utilizing the ABSPATH constant or within the wp-content/uploads folders. However, I sensed the potential for more refined alternatives beyond these choices. Consequently, I resolved to explore solutions crafted by notable industry players. Drawing inspiration from Yoast's practices, I determined that a dynamic approach, involving runtime construction of the sitemap.html and its subsequent provisioning through PHP using rewrite rules (akin to Yoast's handling of the sitemap.xml), presented itself as an optimal pathway. This route not only enhances security measures but also streamlines testing processes and ensures a more straightforward implementation.


###  Schema Implementations
In the context of Java Spring Boot, the conventional practice involves the use of Plain Old Java Objects (POJOs) to construct Entities, which serve as higher-level abstractions for stored data. This approach, characterized by its simplicity and elegance, was transposed into the WordPress ecosystem through the lens of Schemas – a concept originating from WooCommerce schemas. Notably, this concept is not only suitable for the Model Layer within the Model-View-Controller (MVC) design pattern, but it also seamlessly lends itself to constructing HTML components. In the realm of PHP, where associative arrays and generic objects prevail, adopting a well-structured object-oriented approach for data provisioning significantly enhances overall code quality.



### Refinements to Package Template
Considerable adjustments were undertaken within your package template. A pivotal focus rested on modifications to the composer.json configuration, primarily directed at rectifying instances where certain libraries remained elusive. Additionally, I undertook broader amendments to foster an environment conducive to enhanced ease of operation. These adjustments were the initial stride I embarked upon, and a comprehensive outline of the key alterations is available for review in this commit: GitHub Commit. Furthermore, I fine-tuned PHP capabilities configurations to facilitate more streamlined interactions with PHP 7.4 within the context of PHP_CodeSniffer (PHPCS).



### Embracing Testing Paradigms

Drawing inspiration from WP Rocket's rigorous testing methodology, I delved into their testing approach. While I was no stranger to unit testing and occasionally ventured into integration testing for critical pathways, WP Rocket's method of testing the filesystem and employing Brain\Monkey for integration testing struck a chord of fascination. The ensuing codebase bears witness to this inspiration, with substantial segments influenced by WP Rocket's unit and integration testing strategies.

### High-Level View of Technical Decisions
The remaining framework closely aligns with my accumulated experiences and past endeavors.

Operational Dynamics and Rationale
The narrative of the plugin's sequential execution unfolds as follows:

1. The plugin loads and immediately hooks the following actions:
   - The "crawl links" task. It executes the task through Cronjobs. During the "crawl links" task hooking, it schedules a cronjob that will run the task at the plugin's activation. It also unschedules it at the plugin's deactivation.
   - The "Sitemap Manager" page registration.
   - The "crawl links" admin request handler. It executes the task through the administrator's request.
   - The "sitemap.html" router. It serves the sitemap.html.
2. The "crawl links" executions and the sitemap router will use the rest of the structure by demand.

The ensuing sections unravel the remaining facets of the architecture.


### The Crawler Domain
This domain constitutes the crux of downloading web pages and scrutinizing internal links. A notable facet is its scalability, facilitated by the introduction of an interface to potentially accommodate additional crawlers. Notably, the WebpageReader component was dissociated from the Crawler interface, enabling flexible usage of HTML content from the requested page beyond the scope of crawling.

The LinksCrawler facet furnishes an assortment of internal links within the requested webpage.

### The Filesystem Domain
This realm extends provisions for storing static files within the wp-content/uploads/wp-media/ directory. Its utility encompasses storage, retrieval, existence validation, and file deletion.

In the context of "crawl links" executions, it serves as a repository for downloaded pages, aligning with the prescribed technical requirement: 'Save the home page’s .php file as a .html file.'


### The Exceptions Domain
An instrumental facet, the Exceptions domain ensures the delivery of pertinent feedback in cases of potential errors, catering to both administrators executing the "crawl links" task and technical support personnel through comprehensive log entries.

### The Schemas domain

This domain is instrumental in managing links, abstracting the underlying data while furnishing an array of methods that streamline data presentation. This dual-pronged utility proves advantageous for database manipulation and HTML generation.

### The "Crawl Links" execution/task

Here is where the puzzle of classes is solved.

1. First we delete the previous data from the database and the stored webpage html.
2. Request the webpage (for this test, only the home page) using Crawlers\WebpageReader.
3. Retrieve the links inside the webpage using Crawlers\LinksCrawler, which returns a list of Schemas\Link.
4. Store the links in the database using Sitemap\SitemapLinksRouter with a Schemas\LinksRecord object.
5. Store the request page as a .html file using Filesystem\File.

If an error happens, an exception will be thrown. Those exceptions are then caught by the class that triggered the execution/task.



## How your solution achieves the admin’s desired outcome per the user story

I could improve the algorithm that displays the links on the admin page so it would deliver a sitemap much smarter for the admin. The story doesn't cover it, but thinking about SEO it is important to analyze what is not good, as much as we check what is good.

I also think that with some sprints or iterations with the stakeholders, we could dig into the problems and build a much more sophisticated solution.

However, thinking of this task as an MVP (or as a simple approach to the problem solution), it solves the problem with mastery. 
