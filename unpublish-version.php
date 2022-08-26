#!/usr/bin/env php
<?php

if (sizeof($argv) !== 2) {
	fwrite(STDERR, "Usage: unpublish-version.php <version>\n");
	exit(2);
} else if (substr($argv[1], 0, 1) !== "v") {
	fwrite(STDERR, "Version must start with 'v'\n");
	exit(2);
}

$config  = json_decode(file_get_contents(__DIR__."/config.json"), true);
$version = substr($argv[1], 1);
$repos   = $config["git_release_repositories"];
$repos[] = "libnapc";

foreach ($repos as $git_repo_name) {
	fwrite(STDERR, "Cloning into '$git_repo_name'\n");
	system("rm -rf ".escapeshellarg(__DIR__."/tmp/$git_repo_name"));
	clearstatcache();
	mkdir(__DIR__."/tmp/$git_repo_name");
	chdir(__DIR__."/tmp/$git_repo_name");
	system("git init");
	system("git remote add origin git@github.com:libnapc/$git_repo_name.git");

	# remove tag (libnapc repo does not have version tags without "v")
	if ($git_repo_name !== "libnapc") {
		system("git push --delete origin $version");
	}
	# remove branch
	# remove tag (if repo is libnapc)
	system("git push --delete origin v$version");
}
