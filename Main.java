public class Main {

    public static void main(String[] args) {

        if (args.length == 0) {
            System.out.println("Usage:");
            System.out.println("  java Main server");
            System.out.println("  java Main client");
            return;
        }

        if (args[0].equalsIgnoreCase("server")) {
            Server.start();
        }
        else if (args[0].equalsIgnoreCase("client")) {
            ClientGame game = new ClientGame();
            game.start();
        }
        else {
            System.out.println("Unknown option: " + args[0]);
        }
    }
}
