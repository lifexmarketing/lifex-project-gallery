/* ============================================================
   Automated tests for photo-gallery-prototype.html
   Run:  cd <dir with node_modules incl. jsdom>;  HTML_PATH=/abs/photo-gallery-prototype.html node photo-gallery-tests.mjs
   Requires: jsdom  (npm i jsdom)
   Covers engine logic, two-tier (category -> sub-tag) filtering, the
   rendered DOM, and a regression guard for the multi-column + transform
   bug that made images appear only on hover.
   ============================================================ */
import { readFileSync } from "node:fs";
import { JSDOM } from "jsdom";
import path from "node:path";
import { fileURLToPath } from "node:url";

const HERE = path.dirname(fileURLToPath(import.meta.url));
const HTML_PATH = process.env.HTML_PATH || path.join(HERE, "photo-gallery-prototype.html");
const html = readFileSync(HTML_PATH, "utf8");

let pass=0, fail=0; const fails=[];
function ok(name,cond){ if(cond){pass++;} else {fail++;fails.push(name);console.log("  ✗ "+name);} }
function eq(name,a,b){ ok(name+"  ("+JSON.stringify(a)+" === "+JSON.stringify(b)+")", a===b); }

/* ---- 0. Static regression guards ---- */
console.log("Static / regression guards");
ok("grid uses display:grid", /\.grid\s*\{[^}]*display:grid/.test(html));
ok("grid does NOT use multi-column 'columns:'", !/\.grid\s*\{[^}]*[\s;{]columns\s*:/.test(html));
ok("no static translateZ on .card (hover-repaint bug)", !/\.card\s*\{[^}]*translateZ/.test(html));
ok("tiles have always-visible caption (.cap)", /class="cap"/.test(html));
ok("images use object-fit cover", /object-fit:cover/.test(html));
ok("lightbox controls have aria-labels", (html.match(/aria-label=/g)||[]).length>=3);
ok("uses native lazy loading", /loading="lazy"/.test(html));

/* ---- boot ---- */
const dom=new JSDOM(html,{runScripts:"dangerously",pretendToBeVisual:true});
const {window}=dom; const L=window.LXPG; const doc=window.document;
console.log("Engine logic");
ok("LXPG engine exposed", !!L);

/* ---- 1. URL variants ---- */
eq("variant web", L.variant("Patios-42-56.jpg","web"), "Patios-42-56-web.jpg");
eq("variant thumb", L.variant("Patios-42-56.jpg","thumb"), "Patios-42-56-thumb.jpg");
ok("full url -web on Tussey host", L.PHOTOS[0].full.startsWith(L.BASE)&&L.PHOTOS[0].full.includes("-web."));
ok("thumb url -thumb", L.PHOTOS[0].thumb.includes("-thumb."));

/* ---- 2. Taxonomy (two-tier) ---- */
eq("main category count", L.mainCategories().length, 7);
ok("includes expected mains", ["lighting","water features","hardscape","landscape","pools","construction","pavilions & pergolas"].every(m=>L.mainCategories().includes(m)));
eq("hardscape sub-tag count", L.subsFor("hardscape").length, 12);
ok("hardscape subs include patio & fireplace", L.subsFor("hardscape").includes("patio")&&L.subsFor("hardscape").includes("fireplace"));
eq("water features sub-tag count", L.subsFor("water features").length, 12);
ok("water features subs include waterfall & pondless", L.subsFor("water features").includes("waterfall")&&L.subsFor("water features").includes("pondless"));
ok("lighting subs populated", L.subsFor("lighting").length===6);
ok("every taxonomy entry has subs", L.TAXONOMY.every(t=>t.subs.length>0));

/* ---- 3. Photo data ---- */
eq("photo count", L.PHOTOS.length, 15);
eq("tagged photos", L.PHOTOS.filter(p=>p.tagged).length, 14);
ok("one untagged (PANA9641)", L.PHOTOS.filter(p=>!p.tagged).length===1 && L.PHOTOS.find(p=>!p.tagged).title==="PANA9641");
ok("every tagged photo's main is a real category", L.PHOTOS.filter(p=>p.tagged).every(p=>L.mainCategories().includes(p.main)));
ok("every sub-tag belongs to its photo's category", L.PHOTOS.every(p=>p.subs.every(s=>L.subsFor(p.main).includes(s))));

/* ---- 4. Hierarchical filtering ---- */
const wf=L.filterPhotos(L.PHOTOS,{main:"water features",sub:"",loc:"",q:""});
ok("filter by main category", wf.length>0 && wf.every(p=>p.main==="water features"));
const wfFall=L.filterPhotos(L.PHOTOS,{main:"water features",sub:"waterfall",loc:"",q:""});
ok("sub-tag narrows results", wfFall.length>0 && wfFall.length<wf.length && wfFall.every(p=>p.subs.includes("waterfall")));
const hardFire=L.filterPhotos(L.PHOTOS,{main:"hardscape",sub:"fireplace",loc:"",q:""});
ok("category+sub-tag combo", hardFire.length>0 && hardFire.every(p=>p.main==="hardscape"&&p.subs.includes("fireplace")));
eq("empty filter returns all", L.filterPhotos(L.PHOTOS,{main:"",sub:"",loc:"",q:""}).length, 15);
const loc=L.filterPhotos(L.PHOTOS,{main:"",sub:"",loc:"Altoona, PA",q:""});
ok("location filter", loc.length>0 && loc.every(p=>p.loc==="Altoona, PA"));
ok("category with no sample photos yields none", L.filterPhotos(L.PHOTOS,{main:"pools",sub:"",loc:"",q:""}).length===0);

/* ---- 5. Projects (REAL data from /project-showcase/) ---- */
eq("real project count", L.PROJECTS.length, 23);
ok("every project has a cover + link", L.PROJECTS.every(pr=>pr.cover && pr.link));
ok("cover thumb is -735x489 on Tussey host", L.PROJECTS.every(pr=>pr.cover.thumb.startsWith("https://www.tusseylandscaping.com")&&pr.cover.thumb.includes("-735x489")));
ok("cover full strips the size suffix", L.PROJECTS.every(pr=>!pr.cover.full.includes("-735x489")));
ok("project links point at /project/ slug", L.PROJECTS.every(pr=>pr.link.indexOf("https://www.tusseylandscaping.com/project/")===0));
// location parsing from titles
eq("parseLoc comma form", L.parseLoc("Water Feature in James Creek, PA"), "James Creek, PA");
eq("parseLoc out-of-state", L.parseLoc("Large Water Feature with flagstone in Fort Ashby, WV"), "Fort Ashby, WV");
eq("parseLoc no-comma form", L.parseLoc("Backyard Escape with Pond and Flagstone Patio in Nanty Glo PA"), "Nanty Glo, PA");
eq("parseLoc none", L.parseLoc("Swim Recreation Pond Paradise with Outdoor Living Space"), "");
// category inference from titles
eq("inferMain pool", L.inferMain("Complete Backyard Make Over with Fiberglass Swimming Pool in Huntingdon, PA"), "pools");
eq("inferMain swim pond is water (not pool)", L.inferMain("Swim Recreation Pond Paradise with Outdoor Living Space"), "water features");
eq("inferMain hardscape", L.inferMain("Backyard Hardscape Patio with Landscaping and Sidewalk in State College, PA"), "hardscape");
eq("inferMain lighting", L.inferMain("Landscaping and Lighting Project in Duncansville, PA"), "lighting");
eq("slugify matches WP slug", L.slugify("Water Feature in James Creek, PA"), "water-feature-in-james-creek-pa");
ok("every project main is a real category", L.PROJECTS.every(pr=>L.mainCategories().includes(pr.main)));
// filtering (project showcase = category + location, no sub-tags)
const pjPools=L.filterProjects(L.PROJECTS,{main:"pools",sub:"",loc:"",q:""});
ok("project filter by category", pjPools.length>0 && pjPools.every(pr=>pr.main==="pools"));
const pjSC=L.filterProjects(L.PROJECTS,{main:"",sub:"",loc:"State College, PA",q:""});
ok("project filter by town", pjSC.length>0 && pjSC.every(pr=>pr.loc==="State College, PA"));
ok("project locations exclude empty", L.uniqueLocations(L.PROJECTS).every(l=>l&&l.length>0));

/* ---- 6. Rendered DOM ---- */
console.log("Rendered DOM");
const cards=doc.querySelectorAll("#grid .card");
ok("grid rendered cards (not blank)", cards.length>0 && cards.length<=9);
const imgs=doc.querySelectorAll("#grid .card .thumb img");
ok("every card has a -thumb image on Tussey host", imgs.length===cards.length && [...imgs].every(im=>{const s=im.getAttribute("src")||"";return s.startsWith(L.BASE)&&s.includes("-thumb.");}));
ok("every card has a visible caption title", [...cards].every(c=>{const t=c.querySelector(".cap .t");return t&&t.textContent.trim().length>0;}));
eq("main chips = All + 7", doc.querySelectorAll("#catChips .chip").length, 8);
ok("sub-tag row hidden before a category is chosen", doc.getElementById("subRow").hidden===true);
/* simulate choosing a category with sub-tags */
window.state.main="hardscape"; window.buildSubRow();
ok("sub-tag row appears after choosing category", doc.getElementById("subRow").hidden===false);
ok("sub-tag row shows All + 12 hardscape subs", doc.querySelectorAll("#subRow .subchip").length===13);
ok("taxonomy table lists all 7 categories", doc.querySelectorAll("#taxTable tr").length===7);
ok("photo table lists all 15", doc.querySelectorAll("#photoTable tr").length===15);
/* switch to Project mode: real tiles render, sub-tags hidden (Showcase filters by category + town) */
window.setMode("project");
const pcards=doc.querySelectorAll("#grid .card");
ok("project mode renders real project cards", pcards.length>0 && pcards.length<=9);
ok("project tiles use Tussey upload images", [...doc.querySelectorAll('#grid .card img')].every(im=>(im.getAttribute('src')||'').includes('/wp-content/uploads/')));
ok("sub-tag row hidden in project mode", doc.getElementById("subRow").hidden===true);

console.log("\n"+pass+" passed, "+fail+" failed");
if(fail){ console.log("FAILED:\n - "+fails.join("\n - ")); process.exit(1); }
console.log("ALL TESTS PASSED");
