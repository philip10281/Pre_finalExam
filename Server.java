import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.ServerSocket;
import java.net.Socket;
import java.util.Map;
import java.util.concurrent.ConcurrentHashMap;
import java.util.concurrent.atomic.AtomicInteger;

public class Server {

    private static final int PORT = 5000;

    private static final AtomicInteger NEXT_ID =
            new AtomicInteger(1);

    private static final Map<Integer, Player> players =
            new ConcurrentHashMap<>();

    private static final Map<Integer, PrintWriter> clients =
            new ConcurrentHashMap<>();

    public static void main(String[] args) {

        System.out.println();
        System.out.println("================================");
        System.out.println("      3D MULTIPLAYER SERVER");
        System.out.println("================================");
        System.out.println();
        System.out.println("Port: " + PORT);
        System.out.println("Waiting for players...");
        System.out.println();

        try (ServerSocket serverSocket =
                     new ServerSocket(PORT)) {

            while (true) {

                Socket socket =
                        serverSocket.accept();

                int id =
                        NEXT_ID.getAndIncrement();

                Player player =
                        new Player(id);

                players.put(id, player);

                System.out.println(
                        "Player " +
                        id +
                        " connected from " +
                        socket.getInetAddress()
                );

                Thread clientThread =
                        new Thread(
                                new ClientHandler(
                                        socket,
                                        id
                                )
                        );

                clientThread.setName(
                        "Player-" + id
                );

                clientThread.start();
            }

        } catch (IOException e) {

            System.err.println(
                    "Server error:"
            );

            e.printStackTrace();
        }
    }

    // =====================================================
    // CLIENT HANDLER
    // =====================================================

    private static class ClientHandler
            implements Runnable {

        private final Socket socket;
        private final int playerId;

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

                // Tell this player their ID.
                output.println(
                        "WELCOME " + playerId
                );

                // Send existing players to the new player.
                sendExistingPlayers(output);

                // Tell everybody about the new player.
                broadcast(
                        "JOIN " + playerId
                );

                String message;

                while (
                        (message = input.readLine())
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
                        "LEAVE " + playerId
                );

                try {
                    socket.close();
                } catch (IOException ignored) {
                }
            }
        }
    }

    // =====================================================
    // EXISTING PLAYERS
    // =====================================================

    private static void sendExistingPlayers(
            PrintWriter output) {

        for (Player player :
                players.values()) {

            output.println(
                    "PLAYER " +
                    player.id +
                    " " +
                    player.x +
                    " " +
                    player.y +
                    " " +
                    player.z
            );
        }
    }

    // =====================================================
    // HANDLE CLIENT MESSAGE
    // =====================================================

    private static void handleMessage(
            int playerId,
            String message) {

        String[] parts =
                message.trim().split("\\s+");

        if (parts.length == 0) {
            return;
        }

        // -------------------------------
        // POSITION
        // -------------------------------

        if (parts[0].equals("POS")) {

            if (parts.length < 4) {
                return;
            }

            try {

                float x =
                        Float.parseFloat(parts[1]);

                float y =
                        Float.parseFloat(parts[2]);

                float z =
                        Float.parseFloat(parts[3]);

                Player player =
                        players.get(playerId);

                if (player == null) {
                    return;
                }

                player.x = x;
                player.y = y;
                player.z = z;

                broadcastPositions();

            } catch (NumberFormatException ignored) {

                System.out.println(
                        "Invalid position from player "
                                + playerId
                );
            }
        }

        // -------------------------------
        // SHOOT
        // -------------------------------

        else if (parts[0].equals("SHOOT")) {

            broadcast(
                    "SHOOT " + playerId
            );
        }
    }

    // =====================================================
    // BROADCAST POSITIONS
    // =====================================================

    private static void broadcastPositions() {

        StringBuilder packet =
                new StringBuilder("PLAYERS");

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

    // =====================================================
    // BROADCAST MESSAGE
    // =====================================================

    private static void broadcast(
            String message) {

        for (PrintWriter client :
                clients.values()) {

            client.println(message);
        }
    }

    // =====================================================
    // PLAYER DATA
    // =====================================================

    private static class Player {

        final int id;

        float x;
        float y;
        float z;

        int health;

        Player(int id) {

            this.id = id;

            this.x = 0;
            this.y = 1;
            this.z = 0;

            this.health = 100;
        }
    }
}
