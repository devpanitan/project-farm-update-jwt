{ pkgs, ... }:

{ 
  # Pinned to a version that is compatible with the version of Vite used in the project
  languages.nix.package = pkgs.nodejs_22;
  
  # The following are other examples of what you can configure in your dev environment.
  #
  # # To find more configuration options, visit https://devenv.sh/reference/options/
  #
  # # Processes to run when you enter the environment
  # enterShell = ''
  #   echo "Hello, world!"
  # '''
  #
  # # Environment variables
  # env.GREET = "Hello from the environment";
}
