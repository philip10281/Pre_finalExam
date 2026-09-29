import java.io.*;
import java.net.*;
import java.util.*;
import java.util.concurrent.*;

public class Main {

    // =========================
    // SERVER SETTINGS
    // =========================

    static final int PORT = 5000;

    static final Map<Integer, Player> players =
            new ConcurrentHashMap<>();

    static final Map<Integer, PrintWriter> clients =
            new ConcurrentHashMap<>();

    static int nextPlayerId = 1;

    // =========================
    // MAIN
    // =========================

    public static void main(String[] args) {

        if (args.length == 0) {
            System.out.println("Usage:");
            System.out.println("  java Main server");
            System.out.println("  java Main client");
            return;
        }

        if (args[0].equalsIgnoreCase("server")) {
            startServer();
        }

        else if (args[0].equalsIgnoreCase("client")) {
            startClient();
        }

        else {
            System.out.println("Unknown command: " + args[0]);
        }
    }

    // =========================
    // SERVER
    // =========================

    static void startServer() {

        System.out.println("==============================");
        System.out.println("     3D MULTIPLAYER SERVER");
        System.out.println("==============================");
        System.out.println("Port: " + PORT);
        System.out.println("Waiting for players...");
        System.out.println();

        try (ServerSocket serverSocket =
                     new ServerSocket(PORT)) {

            while (true) {

                Socket socket =
                        serverSocket.accept();

                int id = nextPlayerId++;

                Player player =
                        new Player(id);

                players.put(id, player);

                System.out.println(
                        "Player " + id +
                        " connected: " +
                        socket.getInetAddress()
                );

                Thread thread =
                        new Thread(
                                new ClientHandler(
                                        socket,
                                        id
                                )
                        );

                thread.start();
            }

        } catch (IOException e) {

            System.out.println(
                    "Server error:"
            );

            e.printStackTrace();
        }
    }

    // =========================
    // CLIENT HANDLER
    // =========================

    static class ClientHandler
            implements Runnable {

        Socket socket;
        int playerId;

        ClientHandler(
                Socket socket,
                int playerId) {

            this.socket = socket;
            this.playerId = playerId;
        }

        @Override
        public void run() {

            try {

                BufferedReader input =
                        new BufferedReader(
                                new InputStreamReader(
                                        socket.getInputStream()
                                )
                        );

                PrintWriter output =
                        new PrintWriter(
                                socket.getOutputStream(),
                                true
                        );

                clients.put(
                        playerId,
                        output
                );

                // Tell client its ID

                output.println(
                        "WELCOME " +
                        playerId
                );

                broadcast(
                        "JOIN " +
                        playerId
                );

                String message;

                while (
                        (message =
                                input.readLine())
                                != null
                ) {

                    handleMessage(
                            playerId,
                            message
                    );
                }

            } catch (IOException e) {

                System.out.println(
                        "Player " +
                        playerId +
                        " disconnected."
                );

            } finally {

                players.remove(playerId);

                clients.remove(playerId);

                broadcast(
                        "LEAVE " +
                        playerId
                );

                try {
                    socket.close();
                } catch (IOException ignored) {
                }
            }
        }
    }

    // =========================
    // NETWORK MESSAGES
    // =========================

    static void handleMessage(
            int playerId,
            String message) {

        String[] parts =
                message.split(" ");

        if (parts.length == 0)
            return;

        // -------------------------
        // POSITION
        // -------------------------

        if (parts[0].equals("POS")) {

            if (parts.length < 4)
                return;

            try {

                float x =
                        Float.parseFloat(parts[1]);

                float y =
                        Float.parseFloat(parts[2]);

                float z =
                        Float.parseFloat(parts[3]);

                Player player =
                        players.get(playerId);

                if (player != null) {

                    player.x = x;
                    player.y = y;
                    player.z = z;
                }

                broadcastPositions();

            } catch (NumberFormatException ignored) {
            }
        }

        // -------------------------
        // SHOOT
        // -------------------------

        else if (parts[0].equals("SHOOT")) {

            broadcast(
                    "SHOOT " +
                    playerId
            );
        }
    }

    // =========================
    // BROADCAST POSITIONS
    // =========================

    static void broadcastPositions() {

        StringBuilder packet =
                new StringBuilder();

        packet.append("PLAYERS");

        for (Player player :
                players.values()) {

            packet.append(" ")
                    .append(player.id)
                    .append(" ")
                    .append(player.x)
                    .append(" ")
                    .append(player.y)
                    .append(" ")
                    .append(player.z);
        }

        broadcast(packet.toString());
    }

    // =========================
    // BROADCAST
    // =========================

    static void broadcast(
            String message) {

        for (PrintWriter client :
                clients.values()) {

            client.println(message);
        }
    }

    // =========================
    // PLAYER
    // =========================

    static class Player {

        int id;

        float x = 0;
        float y = 1;
        float z = 0;

        int health = 100;

        Player(int id) {
            this.id = id;
        }
    }

    // =========================
    // CLIENT
    // =========================

    static void startClient() {

        System.out.println(
                "Client mode requires the " +
                "jMonkeyEngine client implementation."
        );

        System.out.println();
        System.out.println(
                "Use:"
        );

        System.out.println(
                "java Main server"
        );

        System.out.println(
                "to start the multiplayer server."
        );
    }
}
